<?php

namespace App\Services;

use App\Models\Active\Active;
use App\Models\Active\MoneyActive\BankMoneyActive;
use App\Models\Active\MoneyActive\NonBankMoneyActive;
use App\Models\Active\NonMoneyActive\CountableNonMoneyActive;
use App\Models\Active\NonMoneyActive\NonMoneyActive;

final class ActiveService
{

    private string $storageFile = __DIR__ . '/../storage/actives.json';
    private array $actives;

    public function __construct()
    {
        $this->actives = $this->loadActives();
    }

    public function getActives(): array
    {
        return $this->actives;
    }

    public function storeActive(array $data): bool
    {
        $data['id'] = $this->getNextId();
        $active = $this->getNewActive($data);
        $this->actives[] = $active;
        $this->saveActives();
        return true;
    }

    public function updateActive(array $data): bool
    {
        $id = (int)$data['id'];
        foreach ($this->actives as &$active) {
            if ($active['id'] == $id) {
                $active = $data;
                $this->saveActives();
                return true;
            }
        }
        return false;
    }

    public function deleteActive($id): bool
    {
        foreach ($this->actives as $key => $active) {
            if ($active['id'] == $id) {
                unset($this->actives[$key]);
                $this->saveActives();
                return true;
            }
        }
        return false;
    }

    private function getNewActive(array $data): Active
    {
        if ($data['type'] == 'money') {
            if ($data['bankName']) {
                return new BankMoneyActive(...$data);
            }
            return new NonBankMoneyActive(...$data);
        }
        if ($data['inventoryNumber'] || $data['measureUnits'] || $data['productionDate']) {
            return new CountableNonMoneyActive(...$data);
        }
        return new NonMoneyActive(...$data);
    }

    private function loadActives(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $json = file_get_contents($this->storageFile);
        return json_decode($json, true) ?? [];
    }

    private function saveActives(): void
    {
        file_put_contents($this->storageFile, json_encode($this->actives, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function getNextId(): int
    {
        $lastId = $this->actives[array_key_last($this->actives)]['id'];
        return $lastId + 1;

    }
}