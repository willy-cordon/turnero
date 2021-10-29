<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\DestroyModel;
use Zendaemon\Services\Traits\UpdateModel;

final class ClientService extends Service
{
    use DestroyModel, UpdateModel;
    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Client::class;
    }

    /**
     * @return Collection
     */
    public function all():Collection
    {
        return $this->model::all();
    }

    public function create(Request $request): Model
    {
        $client = $this->model::create($request->all());
        $client->api_token = str_random(60);
        $client->save();
        return $client;
    }

}
