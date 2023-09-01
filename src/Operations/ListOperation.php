<?php



namespace Phaney\ApiCrud\Operations;

use Phaney\ApiCrud\Traits\Filter;
use Phaney\ApiCrud\Traits\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait ListOperation
{
    use Query, Filter;
    public const COLUMN_TYPE_CLOSER = 'closure';
    public const COLUMN_TYPE_DATE = 'date';
    public const COLUMN_TYPE_TIME = 'time';
    public static int $limit = 20;
    protected array $columns = [];
    protected $listPermissions = [];

    /**
     * Define which routes are needed for this operation.
     */
    public static function setupListRoutes(string $prefixRouteName)
    {
        Route::get('/', [self::class, 'index'])->name($prefixRouteName . 'list');
    }

    protected function setListPermissions(array $permissions)
    {
        $this->listPermissions = $permissions;
    }

    public function setUpListOperation()
    {
           // for override
    }

    public function addColumn($name, $column = [])
    {
        $this->columns[] = [
            'name' => $name,
            ...$column
        ];
    }

    public function addColumns(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->verifyPermissions($this->getListPermissions());

        $this->query = $this->model::query();
        $this->setUpListOperation();

        $limit = $request->all()['limit'] ?? self::$limit;
        $order = $request->all()['order'] ?? null;

        if ($order !== null) {
            $order = explode(',', $order);
        }

        $order[0] = $order[0] ?? 'id';
        $order[1] = $order[1] ?? 'asc';


        $this->applyFilters();

        $result = $this->query->orderBy($order[0], $order[1])
            ->paginate($limit);

        $result->getCollection = $result->getCollection()
            ->map([$this, 'transformColumn']);

        return $this->successResponse($result);
    }

    public function getListPermissions() : array
    {
        return [];
    }

    public function transformColumn($item)
    {
        $returnValue = [];

        foreach($this->columns as $column) {
            $field = $column['field'] ?? $column['name'] ?? $column;
            $name = $column['name'] ?? $column;

            switch($column['type'] ?? 'text') {
                case self::COLUMN_TYPE_CLOSER:
                    $returnValue[$name] = call_user_func($column['function'], $item);
                    break;

                case self::COLUMN_TYPE_DATE:
                    $returnValue[$name] = $item->{$field}?->setTimezone(config('settings.timezone'))
                        ?->format(config('settings.date_format'));
                    break;

                case self::COLUMN_TYPE_TIME:
                    $returnValue[$name] = $item->{$field}?->setTimezone(config('settings.timezone'))
                        ?->format(config('settings.date_time_format'));
                    break;
                default:
                    $returnValue[$name] = $item->{$field};
            }
        }

        return $returnValue;
    }
}