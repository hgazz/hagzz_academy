<?php

namespace App\Http\Traits;

trait DataTablesTrait
{
    private function makeHideButtons($array): array
    {
        $data = [];

        foreach ($array as $index => $element) {
            $data[] = [
                'text' => $element,
                'className' => "dt-button btn btn-primary btn-sm toggle-vis me-1 mt-2 mb-2",
                'action' => "function(e, dt, node, config) {
                    var column = dt.column($index);
                    var isVisible = column.visible();
                    column.visible(!isVisible);
                    if (!isVisible) {
                        $(node).removeClass('btn-default').addClass('btn-primary');
                    } else {
                        $(node).removeClass('btn-primary').addClass('btn-default');
                    }
                }"
            ];
        }

        // Add print button
        $data[] = [
            'extend' => 'print',
            'className' => 'dt-button btn btn-dark btn-sm toggle-vis me-1 mt-2 mb-2',
            'text' =>  trans('admin.print'),
        ];

        // Add excel export button
        $data[] = [
            'extend' => 'excel',
            'className' => 'dt-button btn btn-success btn-sm toggle-vis me-1 mt-2 mb-2',
            'text' => trans('admin.export')
        ];

        return $data;
    }

}
