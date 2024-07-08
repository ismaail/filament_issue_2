<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\Section::make('Variants')
                    ->heading(function (array $state) {
                        $repeaterCount = count($state['variants_repeater'] ?? []);
                        return "Variants ({$repeaterCount})";
                    })
                    ->compact()
                    ->schema([
                        Forms\Components\Repeater::make('variants_repeater')
                            ->relationship('variants')
                            ->hiddenLabel()
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                            ->collapsible()
                            ->grid(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')->required(),
                            ])
                    ]),
                Forms\Components\Section::make('Stocks')
                    ->heading(function (array $state) {
                        $repeaterCount = count($state['stocks_repeater'] ?? []);
                        return "Stocks ({$repeaterCount})";
                    })
                    ->compact()
                    ->schema([
                        Forms\Components\Repeater::make('stocks_repeater')
                            ->relationship('stocks')
                            ->hiddenLabel()
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                            ->collapsible()
                            ->grid(2)
                            ->schema([
                                Forms\Components\TextInput::make('quantity')->required(),
                                Forms\Components\Select::make('variants')
                                    ->relationship(
                                        name: 'variants',
                                        titleAttribute: 'title',
                                        modifyQueryUsing: function (Builder $builder, Stock $record) {
                                            $builder->where('product_id', '=', $record->product_id);
                                        },
                                    )
                                    ->options(function (Stock $record) {
                                        return Variant
                                            ::where('product_id', '=', $record->product_id)
                                            ->pluck('title', 'id');
                                    })
                                    ->multiple()
                                ,
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
