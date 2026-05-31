<?php

namespace App\Filament\Concerns;

use App\Models\UserTableColumnPreference;

trait PersistsTableColumnsForAuthenticatedUser
{
    protected function loadTableColumnsFromSession(): array
    {
        $preference = $this->getStoredTableColumnPreference();

        if ($preference !== null) {
            session()->put(
                $this->getHasReorderedTableColumnsSessionKey(),
                $preference->has_reordered_columns,
            );

            return $preference->columns;
        }

        return session()->get(
            $this->getTableColumnsSessionKey(),
            $this->getDefaultTableColumnState(),
        );
    }

    protected function persistTableColumns(): void
    {
        if ($this->getTable()->persistsColumnsInSession()) {
            session()->put(
                $this->getTableColumnsSessionKey(),
                $this->tableColumns,
            );
        }

        $userId = auth()->id();

        if (blank($userId)) {
            return;
        }

        UserTableColumnPreference::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'table_key' => $this->getTableColumnPreferenceKey(),
            ],
            [
                'columns' => $this->tableColumns,
                'has_reordered_columns' => (bool) session()->get(
                    $this->getHasReorderedTableColumnsSessionKey(),
                ),
            ],
        );
    }

    protected function getTableColumnPreferenceKey(): string
    {
        return md5(static::class);
    }

    protected function getStoredTableColumnPreference(): ?UserTableColumnPreference
    {
        $userId = auth()->id();

        if (blank($userId)) {
            return null;
        }

        return UserTableColumnPreference::query()
            ->where('user_id', $userId)
            ->where('table_key', $this->getTableColumnPreferenceKey())
            ->first();
    }
}
