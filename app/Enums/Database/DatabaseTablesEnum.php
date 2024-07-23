<?php

namespace App\Enums\Database;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum DatabaseTablesEnum
{
    use EnumToDatabaseColumnName;

    /****** Defaults *******/
    case Jobs;
    case JobBatches;
    case FailedJobs;
    case Migrations;
    case PasswordResetTokens;
    case PersonalAccessTokens;
    case Sessions;
    case Users;
    /****** Defaults END*******/

    /****** Packages *******/
    case Notifications;
    /****** Packages END*******/

    /****** General *******/
    case UserSettings;
    case ApiNewAttrinutes;
    case Verifications;
    case CustomizedPages;
    /****** General END *******/

    /****** BackOffice *******/
    case Settings;
    case TechnicalSettings;
    case PersonnelExtras;
    case Roles;
    case Permissions;
    case PermissionRole;
    case PostGroups;
    case Posts;
    case ClientCategoryMaps;
    case ClientTrustScores;
    case DynamicDatas;
    case Chatbots;
    case ChatbotSteps;
    case ChatbotTesters;
    case ChatbotChats;
    case ChatbotMessages;
    case Tickets;
    case TicketMessages;
    case DomainHolders;
    case DomainExtensions;
    case DomainCategories;
    case DomainHolderAccounts;
    case Domains;
    case AssignedDomains;
    case DedicatedDomains;
    case Referrals;
    case ReferralRewardPackages;
    case ReferralRewardItems;
    case ReferralCustomSettings;
    case ReferralSessions;
    case ReferralBetsConclusions;
    case ReferralClaimedRewards;
    case ReferralRewardConclusions;
    case ReferralRewardPayments;
    case CurrencyRates;
    case Bets;
    case BetSelections;
    case ClientSyncs;
    /****** BackOffice END *******/

    /****** Site *******/
    case BetconstructClients; // Based on Betconstruct External admin api
    case BetconstructSwarmClients; // Based on Betconstruct Swarm api
    case PostSpacesPermissions;
    case Likes;
    case Comments;
    /****** Site END *******/

    /**
     * Get database table name
     *
     * @param  bool $usePrefix
     * @return string
     */
    public function tableName(bool $usePrefix = false): string
    {
        return $usePrefix ? config('database.connections.mysql.prefix') . $this->dbName() : $this->dbName();
    }
}
