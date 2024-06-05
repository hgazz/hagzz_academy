<?php

namespace App\Http\Traits;

trait DataTablesTrait
{
    private function makeHideButtons($array)
    {
        $data = [];
        foreach($array as $index => $element)
        {
            $data []= [
                'text' => $element,
                'className' => "dt-button btn btn-primary btn-sm toggle-vis mb-3 me-1",
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
        return $data;
    }

}
