<?php

namespace Yab\Quazar\Repositories;

use Illuminate\Support\Facades\Schema;
use Yab\Quazar\Models\Orders;

class OrderRepository
{
    public function __construct(Orders $model)
    {
        $this->model = $model;
    }

    /**
     * Returns all Orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->model->orderBy('created_at', 'desc')->all();
    }

    /**
     * Returns all paginated Orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function paginated()
    {
        return $this->model->orderBy('created_at', 'desc')->paginate(25);
    }

    /**
     * Searches the orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function search($payload)
    {
        $query = $this->model->orderBy('created_at', 'desc');

        $columns = Schema::getColumnListing('orders');
        $query->where('id', '>', 0);
        $query->where('id', 'LIKE', '%'.$payload.'%');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$payload.'%');
        }

        return [$query, $payload, $query->paginate(25)->render()];
    }

    /**
     * Stores Orders into database.
     *
     * @param array $payload
     *
     * @return Orders
     */
    public function store($payload)
    {
        return $this->model->create($payload);
    }

    /**
     * Find Orders by given id.
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection|null|static|Orders
     */
    public function findOrdersById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find Orders by given id.
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection|null|static|Orders
     */
    public function getByCustomer($id)
    {
        return $this->model->where('user_id', '=', $id);
    }

    /**
     * Find Orders by given id.
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection|null|static|Orders
     */
    public function getByCustomerAndId($customer, $id)
    {
        return $this->model->where('user_id', $customer)->where('id', $id)->first();
    }

    /**
     * Find Orders by given id.
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection|null|static|Orders
     */
    public function getByCustomerAndUuid($customer, $id)
    {
        return $this->model->where('user_id', $customer)->where('uuid', $id)->first();
    }

    /**
     * Updates Orders into database.
     *
     * @param Order $order
     * @param array $payload
     *
     * @return Orders
     */
    public function update($order, $payload)
    {
        if (isset($payload['is_shipped'])) {
            $payload['is_shipped'] = true;
        } else {
            $payload['is_shipped'] = false;
        }

        return $order->update($payload);
    }
}
