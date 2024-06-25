<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PatientAppointment;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PatientAppointmentResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\PatientAppointmentResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class PatientAppointmentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = PatientAppointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->label('Patient Name')
                    ->relationship('patient', 'name'),
                Forms\Components\Select::make('doctor_id')
                    ->label('Doctor Name')
                    ->relationship(name: 'doctor', titleAttribute: 'name'),
                Forms\Components\DatePicker::make('date_of_appointment'),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('prescription')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'Jadwal Dibuat' => 'Jadwal Dibuat',
                        'Sudah Diperiksa' => 'Sudah Diperiksa',
                        'Obat Diserahkan' => 'Obat Diserahkan',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $isDoctor   = Auth::user()->hasRole('Dokter');
                $isApoteker = Auth::user()->hasRole('Apoteker');

                if ($isDoctor) {
                    $userId = Auth::user()->id;
                    $query->where('doctor_id', $userId)->where('status', 'Jadwal Dibuat');
                }

                if ($isApoteker) {
                    $query->where('status', 'Sudah Diperiksa')->orWhere('status', 'Obat Diserahkan');
                }
            })

            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_appointment')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientAppointments::route('/'),
            'create' => Pages\CreatePatientAppointment::route('/create'),
            'view' => Pages\ViewPatientAppointment::route('/{record}'),
            'edit' => Pages\EditPatientAppointment::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}