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
                'table'     => 'min-w-full',
                'container' => 'overflow-x-auto',
                'actions'   => 'flex gap-2',
            ],

            'header' => [
                'thead'    => 'border-b border-gray-200',
                'tr'       => '',
                'th'       => 'px-6 py-3 text-left text-xs font-medium text-gray-400 tracking-wide whitespace-nowrap',
                'thAction' => 'px-6 py-3 text-left text-xs font-medium text-gray-400',
            ],

            'body' => [
                'tbody'              => 'bg-white',
                'tbodyEmpty'         => '',
                'tr'                 => 'border-b border-gray-100 hover:bg-gray-50/60 transition-colors duration-100',
                'td'                 => 'px-6 py-4 text-sm text-gray-700 whitespace-nowrap',
                'tdEmpty'            => 'px-6 py-4 text-sm text-gray-400 whitespace-nowrap',
                'tdSummarize'        => 'px-6 py-3 text-sm text-gray-400 text-right',
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'flex gap-2 items-center',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view'   => $this->root() . '.footer',
            'select' => 'text-sm border border-gray-200 rounded-md px-3 py-1.5 bg-white text-gray-600 focus:outline-none focus:ring-1 focus:ring-gray-300 cursor-pointer',
            'span'   => 'text-sm text-gray-600',
            'background' => 'bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between sm:px-6',
            'pagination' => 'flex items-center gap-1', // Esto controla la paginación
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1.5',
        ];
    }

    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full',
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
            'th'    => 'px-6 py-3',
            'base'  => '',
            'label' => 'flex items-center',
            'input' => 'rounded-full border-gray-300 focus:ring-gray-300 h-4 w-4 cursor-pointer',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input'      => 'text-sm border border-gray-200 rounded-md px-3 py-1.5 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 pl-8',
            'iconClose'  => 'text-gray-300 hover:text-gray-400',
            'iconSearch' => 'text-gray-300 mr-2 w-4 h-4',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => 'min-w-[5rem]',
            'select' => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'flatpickr flatpickr-input text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-auto',
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
            'input' => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full min-w-[5rem]',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => 'min-w-[9.5rem]',
            'select' => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full',
            'input'  => 'text-sm border border-gray-200 rounded-md bg-white text-gray-700 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-gray-300 w-full',
        ];
    }
}
