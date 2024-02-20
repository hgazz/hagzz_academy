<?php

namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslatableService
{
    public static function generateTranslatableFields(array $columns, array $validatedData): array
    {
        $fields = [];

        foreach ($columns as $column) {
            $langs = ['en', 'ar'];
            foreach ( $langs as $lang) {
                $fields[$column][$lang] = $validatedData[$column . '_' . $lang];
            }
        }

        return $fields;
    }

    public static function getTranslatableInputs($record): array
    {
        $fields = [];

        foreach($record::$translatableColumns as $key => $attribute) {
            foreach(LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
                $fields[$key . "_" . $lang] = $attribute;
            }
        }

        return $fields;
    }

    public static function getTranslatableFields(array $columns, Model $record, $withEntireRecordData = true): array
    {
        $fields = [];


        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
            foreach ($columns as $column) {
                $fields[$column . "_" . $lang] = $record->getTranslation($column, $lang);
            }
        }

        if ($withEntireRecordData) $fields = array_merge(Arr::except($record->toArray(), $columns), $fields);

        return $fields;
    }

    public static function addTranslatableColumnsToDataTable(Model $model, $table, array $except = [])
    {
        foreach ((new $model)->translatable as $column) {

            if (in_array($column, $except)) continue;

            $lang = LaravelLocalization::getCurrentLocale();
            $table->editColumn($column, function ($data) use ($lang, $column) {
                return Str::limit($data->getTranslation($column, $lang), '50');
            });
        }
    }


    public static function validateTranslatableFields(array $columns)
    {
        $validationRules = [];

        foreach(LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
            foreach($columns as $column => $data) {
                $validationRules[$column . '_' . $lang] = $data['validations'];
            }
        }

        return $validationRules;
    }
}
