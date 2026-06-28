<?php

namespace App\Orchid\Resources;

use Orchid\Crud\Filters\DefaultSorted;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class PostResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Post::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('user_id')
                ->title('Author id')
                ->required(),
            Input::make('title')
                ->title('Title')
                ->required(),
            Input::make('text')
                ->title('Text')
                ->required(),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id'),
            TD::make('user_id', 'Author')
                ->render(function ($model) {
                    return $model->user->name;
                }),
            TD::make('title', 'Title'),
            TD::make('text', 'Text'),
            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id', 'ID'),
            Sight::make('user_id', 'Author')
                ->render(function ($model) {
                    return $model->user->name;
                }),
            Sight::make('title', 'Title'),
            Sight::make('text', 'Text'),
            Sight::make('created_at', 'Creation date')
                ->render(function ($model) {
                    return $model->created_at->format('d.m.Y H:i');
                }),
            Sight::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->format('d.m.Y H:i');
                }),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            new DefaultSorted('created_at', 'desc'),
        ];
    }

    /**
     * Get relationships that should be eager loaded when performing an index query.
     *
     * @return array
     */
    public function with(): array
    {
        return ['user'];
    }

    /**
     * Get the permission key for the resource.
     *
     * @return string|null
     */
    public static function permission(): ?string
    {
        return 'platform.posts.manage';
    }
}
