<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
    use App\Filament\Resources\OrderResource\RelationManagers\AdressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    public function panel(Panel $panel): Panel  
{
    return $panel
        ->resources([
            OrderResource::class,
            // Other resources...
        ]);
}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Group::make()->schema([
                Section::make('Order Information')->schema([
                    Select::make('user_id')->label('Customer')->relationship('user','name')->searchable()->preload()->required(),
                    Select::make('payment_method')->label('Payment method')->options(['stripe'=>'Stripe','cod'=>'Cash on Delivery'])->required(),
                    Select::make('payment_status')->label('Payment method')->options(['pending'=>'Pending','paid'=>'Paid','failed'=>'Faild'])->default('pending')->required(),
                    ToggleButtons::make('status')->inline()->default("new")->options(["new"=>"New","processing"=>"Processing","shipped"=>"Shipped","delivered"=>"Delivered","cancelled"=>"Cancelled"])->icons(["new"=>"heroicon-o-sparkles","processing"=>"heroicon-o-arrow-path","shipped"=>"heroicon-o-arrow-down-circle","delivered"=>"heroicon-o-check-badge","cancelled"=>"heroicon-o-no-symbol"])->colors(["new"=>"info","processing"=>"warning","shipped"=>"info","delivered"=>"success","cancelled"=>"danger"])->required(),
                    Select::make('currency')->options(["usd"=>"USD",'eur'=>"EUR","gbp"=>"GBP"]),
                    Select::make('shipping_method')->options(["fedex"=>"FedEx",'ups'=>"UPS","dhl"=>"DHL",'usps'=>'USPS'])->required(),
                    Textarea::make('notes')->columnSpanFull()
                ])->columns(2),
                Section::make("Order Items")->schema([
                    Repeater::make('items')->relationship()->schema([
                        Select::make('product_id')->relationship('product','name')->
                        searchable()->
                        preload()->
                        required()->
                        distinct()->
                        disableOptionsWhenSelectedInSiblingRepeaterItems()->
                        columnSpan(4)->
                        reactive()->
                        afterStateUpdated(fn ($state, Set $set) => $set('unit_amount',Product::find($state)?->price ?? 0))->
                        afterStateUpdated(fn ($state, Set $set) => $set('total_amount',Product::find($state)?->price ?? 0)),
                        TextInput::make('quantity')->numeric()->required()->default(1)->minValue(1)->columnSpan(2)->
                        reactive()->
                        afterStateUpdated(fn ($state , Set $set , Get $get) => $set('total_amount',$state*$get('unit_amount'))),

                        TextInput::make("unit_amount")->numeric()->required()->dehydrated()->disabled()->columnSpan(3),
                        TextInput::make('total_amount')->numeric()->required()->dehydrated()->columnSpan(3)
                    ])->columns(12),
                    Placeholder::make('grand_total_placeholder')->label('Grand total')->content(function (Get $get , Set $set){
                        $total = 0;
                        if(!$repeaters = $get('items') ){
                            return $total;
                        }
                        foreach($repeaters as $key => $repeater){
                            $total += $get("items.{$key}.total_amount");
                        }
                        $set('grand_total',$total);
                        return Number::currency($total, 'USD');
                    }),
                    Hidden::make('grand_total')->default(0)
                ])
               ])->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('user.name')->label("Customer name")->sortable()->searchable(),
               TextColumn::make('grand_total')->numeric()->sortable()->money(),
               TextColumn::make('payment_method')->sortable()->searchable(),
               TextColumn::make('payment_status')->sortable()->searchable(),    
               TextColumn::make('currency')->sortable()->searchable(),
               TextColumn::make('shipping_method')->sortable()->searchable(),
               SelectColumn::make('status')->options(["new"=>"New","processing"=>"Processing","shipped"=>"Shipped","delivered"=>"Delivered","cancelled"=>"Cancelled"])->sortable()->searchable(),
               TextColumn::make('created_at')->sortable()->toggleable(isToggledHiddenByDefault:true)->dateTime(),
               TextColumn::make('updated_at')->sortable()->toggleable(isToggledHiddenByDefault:true)->dateTime()
               
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AdressRelationManager::class
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success': 'danger';
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\EditOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
