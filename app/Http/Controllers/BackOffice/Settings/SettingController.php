<?php

namespace App\Http\Controllers\BackOffice\Settings;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\SettingsTableEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Settings\SettingRequest;
use App\Models\BackOffice\Settings\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Settings\Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Setting $setting)
    {
        $this->authorize(PermissionAbilityEnum::update->name, Setting::class);

        // To avoid an infinite loop, call this function only here
        $this->updateSettingsTabel();

        $calendarTypeCollection = DropdownListCreater::makeByArray(CalendarTypeEnum::translatedArray())
            ->sort(true)->get();

        $languageCollection = DropdownListCreater::makeByArray(LocaleEnum::translatedArray())
            ->sort(true)->get();

        $files = [];
        foreach (AppSettingsEnum::imageCases() as $case) {

            $files[$case->name] = Setting::getItemFullRecord($case)->getPhotoFileAssistant();
        }

        $data = [
            'AdminRoutesEnum'           => AdminRoutesEnum::class,
            'AppSettingsEnum'           => AppSettingsEnum::class,
            'setting'                   => Setting::getAllSettingsValues(),
            'calendarTypeCollection'    => $calendarTypeCollection,
            'languageCollection'        => $languageCollection,
            'files'                     => $files,
        ];

        return view('hhh.BackOffice.pages.Settings.GeneralSettings.edit.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Settings\SettingRequest  $request
     * @param  \App\Models\BackOffice\Settings\Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request, Setting $setting)
    {
        try {

            $this->updatePhotos($request);

            Setting::updateItems($request->input());
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([$th->getMessage()]);
        }

        return redirect()->back()->withInput()
            ->with('success', trans('PagesContent_GeneralSettings.messages.SavedSuccessfully'));
    }

    /**
     * Update photos
     *
     * @param  \App\Http\Requests\BackOffice\Settings\SettingRequest $inputs
     * @return void
     */
    private function updatePhotos(SettingRequest $request): void
    {
        foreach (AppSettingsEnum::imageCases() as $case) {

            $photoField = $case->name;
            $lastFile = Setting::getItemFullRecord($case)->getPhotoFileAssistant(false);
            $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

            if ($storedFileName != null)
                Setting::set($case, $storedFileName);
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
        foreach (Setting::all() as $item) {

            if (!AppSettingsEnum::hasName($item[SettingsTableEnum::Name->dbName()]))
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
        foreach (AppSettingsEnum::cases() as $case) {

            // To avoid of update existing items, only insert not exist item
            if (!Setting::itemExists($case))
                Setting::add($case, $case->defaultValue(false));
        }
    }
}
