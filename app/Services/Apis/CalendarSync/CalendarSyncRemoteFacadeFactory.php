<?php namespace services\apis\CalendarSync;
/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
use App\Services\Apis\CalendarSync\ICalendarSyncRemoteFacadeFactory;
use models\summit\CalendarSync\CalendarSyncInfo;
use Illuminate\Support\Facades\Log;
/**
 * Class CalendarSyncRemoteFacadeFactory
 * @package services\apis\CalendarSync
 */
final class CalendarSyncRemoteFacadeFactory implements ICalendarSyncRemoteFacadeFactory
{
    /**
     * @param CalendarSyncInfo $sync_calendar_info
     * @return ICalendarSyncRemoteFacade|null
     */
    public function build(CalendarSyncInfo $sync_calendar_info){
        try {
            switch ($sync_calendar_info->getProvider()) {
                case CalendarSyncInfo::ProviderGoogle:
                    return new GoogleCalendarSyncRemoteFacade($sync_calendar_info);
                    break;
                case CalendarSyncInfo::ProvideriCloud:
                    return new ICloudCalendarSyncRemoteFacade($sync_calendar_info);
                    break;
                case CalendarSyncInfo::ProviderOutlook:
                    return new OutlookCalendarSyncRemoteFacade($sync_calendar_info);
                    break;
            }
            return null;
        }
        catch (\Exception $ex){
            Log::warning($ex);
            return null;
        }
    }
}