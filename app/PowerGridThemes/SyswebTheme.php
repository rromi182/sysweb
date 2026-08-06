<?php

namespace App\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class SyswebTheme extends Tailwind
{
    public string $name = 'tailwind';

    public function table(): array
    {
        return [
            'layout' => [
                'base'      => 'align-middle inline-block min-w-full w-full',
                'div'       => 'w-full',
                'table'     => 'min-w-full border-collapse',
                'container' => 'overflow-x-auto',
                'actions'   => 'flex items-center justify-end gap-1',
            ],

            'header' => [
                'thead'    => 'border-b border-border bg-transparent',
                'tr'       => '',
                'th'       => 'h-8 px-3 text-left align-middle font-medium text-muted-foreground text-[11px] uppercase tracking-wider whitespace-nowrap select-none',
                'thAction' => 'h-8 px-3 text-left align-middle font-medium text-muted-foreground text-[11px] uppercase tracking-wider',
            ],

            'body' => [
                'tbody'              => 'bg-transparent',
                'tbodyEmpty'         => '',
                'tr'                 => 'border-b border-border transition-colors hover:bg-muted/40',
                'td'                 => 'px-3 py-2.5 text-sm text-foreground align-middle',
                'tdEmpty'            => 'px-3 py-2.5 text-sm text-muted-foreground align-middle',
                'tdSummarize'        => 'px-3 py-2 text-sm text-muted-foreground text-right align-middle',
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'flex items-center justify-end gap-1',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view'       => $this->root() . '.footer',
            'select'     => 'h-8 rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
            'span'       => 'text-xs text-muted-foreground',
            'background' => 'bg-transparent border-t border-border px-3 py-2.5 flex items-center justify-between',
            'pagination' => 'flex items-center gap-1',
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1',
        ];
    }

    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root() . '.toggleable',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'px-6 py-3',
            'base'  => '',
            'label' => 'flex items-center',
            'input' => 'rounded-full border-gray-300 text-gray-700 focus:ring-gray-300 h-4 w-4 cursor-pointer',
        ];
    }

    public function radio(): array
    {
        return [
            'th'    => 'px-3 py-2',
            'base'  => '',
            'label' => 'flex items-center',
            'input' => 'h-3.5 w-3.5 rounded-full border border-primary shadow focus:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input'      => 'flex h-8 w-64 rounded-md border border-input bg-background px-3 py-1 text-xs shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 pl-8',
            'iconClose'  => 'text-muted-foreground hover:text-foreground h-3.5 w-3.5',
            'iconSearch' => 'text-muted-foreground mr-1.5 h-3.5 w-3.5',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => 'min-w-[5rem]',
            'select' => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'flatpickr flatpickr-input flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.multi-select',
            'base'   => 'inline-block relative w-full',
            'select' => 'mt-1',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view'  => $this->root() . '.filters.number',
            'input' => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring min-w-[5rem]',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => 'min-w-[9.5rem]',
            'select' => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring',
            'input'  => 'flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring',
        ];
    }
}
