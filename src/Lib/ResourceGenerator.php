<?php

namespace Yangze\ModulesHelper\Lib;

use Illuminate\Database\Eloquent\Model;
/* this file is from laravel-admin,thanks */
class ResourceGenerator
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $formats = [
        'form_field'  => "\$form->%s('%s', '%s')",
        'show_field'  => "\$show->%s('%s')",
        'grid_column' => "\$grid->%s('%s')",
    ];

    /**
     * @var array
     */
    private $doctrineTypeMapping = [
        'string' => [
            'enum', 'geometry', 'geometrycollection', 'linestring',
            'polygon', 'multilinestring', 'multipoint', 'multipolygon',
            'point',
        ],
    ];

    /**
     * @var array
     */
    protected $fieldTypeMapping = [
        'ip'       => 'ip',
        'email'    => 'email|mail',
        'password' => 'password|pwd',
        'url'      => 'url|link|src|href',
        'mobile'   => 'mobile|phone',
        'color'    => 'color|rgb',
        'image'    => 'image|img|avatar|pic|picture|cover',
        'file'     => 'file|attachment',
    ];

    /**
     * ResourceGenerator constructor.
     *
     * @param mixed $model
     */
    public function __construct($model)
    {
        $this->model = $this->getModel($model);
    }

    /**
     * @param mixed $model
     *
     * @return mixed
     */
    protected function getModel($model)
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (!class_exists($model) || !is_string($model) || !is_subclass_of($model, Model::class)) {
            throw new \InvalidArgumentException("Invalid model [$model] !");
        }

        return new $model();
    }

    /**
     * @return string
     */
    public function generateForm()
    {
        $reservedColumns = $this->getReservedColumns();

        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name = $column->getName();
            if (in_array($name, $reservedColumns)) {
                continue;
            }
            $type = $column->getType()->getName();
            $default = $column->getDefault();

            $defaultValue = '';

            // set column fieldType and defaultValue
            switch ($type) {
                case 'boolean':
                case 'bool':
                    $fieldType = 'switch';
                    break;
                case 'json':
                case 'array':
                case 'object':
                    $fieldType = 'text';
                    break;
                case 'string':
                    $fieldType = 'text';
                    foreach ($this->fieldTypeMapping as $type => $regex) {
                        if (preg_match("/^($regex)$/i", $name) !== 0) {
                            $fieldType = $type;
                            break;
                        }
                    }
                    $defaultValue = "'{$default}'";
                    break;
                case 'integer':
                case 'bigint':
                case 'smallint':
                case 'timestamp':
                    $fieldType = 'number';
                    break;
                case 'decimal':
                case 'float':
                case 'real':
                    $fieldType = 'decimal';
                    break;
                case 'datetime':
                    $fieldType = 'datetime';
                    $defaultValue = "date('Y-m-d H:i:s')";
                    break;
                case 'date':
                    $fieldType = 'date';
                    $defaultValue = "date('Y-m-d')";
                    break;
                case 'time':
                    $fieldType = 'time';
                    $defaultValue = "date('H:i:s')";
                    break;
                case 'text':
                case 'blob':
                    $fieldType = 'textarea';
                    break;
                default:
                    $fieldType = 'text';
                    $defaultValue = "'{$default}'";
            }

            $defaultValue = $defaultValue ?: $default;

            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['form_field'], $fieldType, $name, $label);

            if (trim($defaultValue, "'\"")) {
                $output .= "->default({$defaultValue})";
            }

            $output .= ";\r\n";
        }

        return $output;
    }

    /**
     * generateShow 
     *
     * @return 
     */
    public function generateShow()
    {
        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name = $column->getName();

            // set column label
            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['show_field'], $name, $label);

            $output .= ";\r\n";
        }

        return $output;
    }

    /**
        * generateGrid 
        *
        * @return 
     */
    public function generateGrid()
    {
        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name = $column->getName();
            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['grid_column'], $name, $label);
            $output .= ";\r\n";
        }

        return $output;
    }

    /**
        * getReservedColumns 
        *
        * @return 
     */
    protected function getReservedColumns()
    {
        return [
            $this->model->getKeyName(),
            $this->model->getCreatedAtColumn(),
            $this->model->getUpdatedAtColumn(),
            'deleted_at',
        ];
    }

    /**
     * Get columns of a giving model.
     *
     * @throws \Exception
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getTableColumns()
    {
        if (!$this->model->getConnection()->isDoctrineAvailable()) {
            throw new \Exception(
                'You need to require doctrine/dbal: ~2.3 in your own composer.json to get database columns. '
            );
        }

        $table = $this->model->getConnection()->getTablePrefix().$this->model->getTable();
        /** @var \Doctrine\DBAL\Schema\MySqlSchemaManager $schema */
        $schema = $this->model->getConnection()->getDoctrineSchemaManager($table);

        // custom mapping the types that doctrine/dbal does not support
        $databasePlatform = $schema->getDatabasePlatform();

        foreach ($this->doctrineTypeMapping as $doctrineType => $dbTypes) {
            foreach ($dbTypes as $dbType) {
                $databasePlatform->registerDoctrineTypeMapping($dbType, $doctrineType);
            }
        }

        $database = null;
        if (strpos($table, '.')) {
            list($database, $table) = explode('.', $table);
        }

        return $schema->listTableColumns($table, $database);
    }

    /**
     * Format label.
     *
     * @param string $value
     *
     * @return string
     */
    protected function formatLabel($value)
    {
        return ucfirst(str_replace(['-', '_'], ' ', $value));
    }

    /**
     * generateHtmlHead 
     *
     * @return 
     */
    public function generateHtmlHead()
    {
        $maxKey = 5;
        $columns = $this->getTableColumns();
        $html = '';
        foreach($columns as $column) {
            if (!$maxKey--) {
                break;
            }
            $html .= '<th scope="col">' . $column->getName() . '</th>' . "\n";
        }
        $html .= '<th scope="col">操作</th>' . "\n";
        return $html;
    }

    /**
     * generateHtmlList 
     *
     * @return 
     */
    public function generateHtmlList()
    {
        $maxKey = 5;
        $columns = $this->getTableColumns();
        $html = '';
        foreach($columns as $column) {
            if (!$maxKey--) {
                break;
            }
            if (strtolower($column->getName()) == 'id') {
                $html .= '<td scope="col"><a href="{{ route("DummyRouteShow", $item->' . $column->getName() . ') }}">{{ str_limit($item->' . $column->getName() . ', 25) }}</a></td>' . "\n";
            } else {
                $html .= '<td scope="col">{{ str_limit($item->' . $column->getName() . ', 25) }}</td>' . "\n";
            }
        }
        $html .= '<td scope="col">
            <form method="post" action="{{ route("DummyRouteDestroy", $item->id) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <a class="btn btn-info" href="{{ route("DummyRouteShow", $item->id) }}" role="button">查看</a>&nbsp;
                <a class="btn btn-primary" href="{{ route("DummyRouteEdit", $item->id) }}" role="button">编辑</a>&nbsp;
                <button type="submit" class="btn btn-danger">删除</button>
            </form>
            </td>' . "\n";
        return $html;
    }

    /**
     * generateHtmlView 
     *
     * @return 
     */
    public function generateHtmlView()
    {
        $columns = $this->getTableColumns();
        $html = '';
        foreach($columns as $column) {
            $html .= "<h3>" . $column->getName() . "</h3>\n";
            $html .= '<div class="well">{{ DummyModelClassName->' . $column->getName() . ' }}</div>' . "\n";
        }
        return $html;
    }

    /**
     * generateHtmlForm 
     *
     * @return 
     */
    public function generateHtmlForm()
    {
        $columns = $this->getTableColumns();
        $html = '';
        foreach($columns as $column) {
            $html .= '<div class="form-group">' . "\n";
            $html .= '<label for="inputEmail3" class="col-sm-2 control-label">' . $column->getName() . '</label>' . "\n";
            $html .= '<div class="col-sm-10">' . "\n";

            $type = 'text';
            switch($column->getType()->getName()) {
                case 'integer':
                case 'bigint':
                case 'smallint':
                case 'timestamp':
                    $type = 'number';
                    break;
                case 'text':
                case 'blob':
                    $type = 'textarea';
                    break;
            }

            if ($type != 'textarea') {
                $html .= '<input type="' . $type . '" class="form-control" id="' . $column->getName() . '" name="' . $column->getName() . '" placeholder="' . $column->getName() . '" value="{{ array_get(DummyModelClassName, "' . $column->getName() . '") }}">' . "\n";
            } else {
                $html .= '<textarea class="form-control" rows="3" name="' . $column->getName() . '">{{ array_get(DummyModelClassName, "' . $column->getName() . '") }}</textarea>' . "\n";
            }

            $html .= '</div>' . "\n";
            $html .= '</div>' . "\n";
        }
        return $html;
    }
}
