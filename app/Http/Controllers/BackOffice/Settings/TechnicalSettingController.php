<?php

namespace App\Http\Controllers\BackOffice\Settings;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\TechnicalSettingsTableEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Settings\TechnicalSettingRequest;
use App\Models\BackOffice\Settings\TechnicalSetting;
use Illuminate\Http\Request;

class TechnicalSettingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Settings\TechnicalSetting $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, TechnicalSetting $setting)
    {
        $this->authorize(PermissionAbilityEnum::update->name, TechnicalSetting::class);

        // To avoid an infinite loop, call this function only here
        $this->updateSettingsTabel();

        $data = [
            'setting'   => TechnicalSetting::getAllSettingsValues(),
        ];

        return view('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Settings\TechnicalSettingRequest  $request
     * @param  \App\Models\BackOffice\Settings\TechnicalSetting $setting
     * @return \Illuminate\Http\Response
     */
    public function update(TechnicalSettingRequest $request, TechnicalSetting $setting)
    {
        try {

            $this->updatePhotos($request);

            TechnicalSetting::updateItems($request->input());
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([$th->getMessage()]);
        }

        return redirect()->back()->withInput()
            ->with('success', trans('PagesContent_TechnicalSettings.messages.SavedSuccessfully'));
    }

    /**
     * Update photos
     *
     * @param  \App\Http\Requests\BackOffice\Settings\TechnicalSettingRequest $inputs
     * @return void
     */
    private function updatePhotos(TechnicalSettingRequest $request): void
    {
        foreach (AppTechnicalSettingsEnum::imageCases() as $case) {

            $photoField = $case->name;
            $lastFile = TechnicalSetting::getItemFullRecord($case)->getPhotoFileAssistant(false);
            $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

            if ($storedFileName != null)
                TechnicalSetting::set($case, $storedFileName);
        }
    }

    /**
     * Clear database table and update base on Enum
     *
     * @return void
     */
    private function updateSettingsTabel(): void
    {
        $this->clearDeletedSettings();
        $this->updateSettings();
    }

    /**
     * Clear settings table from deleted settings in Enums
     *
     * @return void
     */
    private function clearDeletedSettings(): void
    {
        foreach (TechnicalSetting::all() as $item) {

            if (!AppTechnicalSettingsEnum::hasName($item[TechnicalSettingsTableEnum::Name->dbName()]))
                $item->delete();
        }
    }

    /**
     * Update settings table in database whit new items in enum
     *
     * @return void
     */
    private function updateSettings(): void
    {
        foreach (AppTechnicalSettingsEnum::cases() as $case) {

            // To avoid of update existing items, only insert not exist item
            if (!TechnicalSetting::itemExists($case))
                TechnicalSetting::set($case, $case->defaultValue(false));
        }
    }
}
