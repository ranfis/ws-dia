<?php
namespace Config;

class Routes{
    const USER_LOGIN            = "/login";
    const USER_SUMMARY          = "/summary";
    const USER_LOGOUT           = "/logout";

    const PROFILE_CHANGE_PASSWORD = "/profile/change_password";
    const PROFILE_UPDATE_INFO     = "/profile/update_info";


    const ADM_USER_ADD          = "/user/add";
    const ADM_USER_UPDATE       = "/user/update";
    const ADM_USER_DEL          = "/user/del";
    const ADM_USER_LIST         = "/user/list";
    const ADM_USER_CHANGE_PASSWORD = "/user/change_password";

    const DASHBOARD             = "/dashboard";

    //Congress
    //POST
    const  SPONSOR_ADD          = "/sponsor/add";
    //PUT
    const  SPONSOR_UPDATE       = "/sponsor/update";
    //GET
    const  SPONSOR_LIST         = "/sponsor/list";

    const  SPONSOR_DEL          = "/sponsor/del";

    //POST
    const  PARTICIPANTS_ADD     = "/participant/add";
    //PUT
    const  PARTICIPANTS_UPDATE  = "/participant/update";
    //GET
    const  PARTICIPANTS_LIST    = "/participant/list";

    //PUT
    const  PARTICIPANTS_DEL     = "/participant/del";

    //POST
    const  CONGRESS_ADD         = "/congress/add";
    //PUT
    const  CONGRESS_UPDATE      = "/congress/update";
    //GET
    const  CONGRESS_LIST        = "/congress/list";

    //PUT
    const  CONGRESS_DEL         = "/congress/del";
    //end: Congress


    //POST
    const JOURNAL_ADD           = "/journal/add";
    //PUT
    const JOURNAL_UPDATE        = "/journal/update";
    //POST
    const JOURNAL_DEL           = "/journal/del";
    //GET
    const JOURNAL_LIST          = "/journal/list";

    //POST
    const ARTICLE_ADD           = "/article/add";
    //PUT
    const ARTICLE_UPDATE        = "/article/update";
    //POST
    const ARTICLE_DEL           = "/article/del";
    //GET
    const ARTICLE_LIST          = "/article/list";


    //POST
    const FUND_ADD              = "/fund/add";
    //PUT
    const FUND_UPDATE           = "/fund/update";
    //POST
    const FUND_DEL              = "/fund/del";
    //GET
    const FUND_LIST             = "/fund/list";

    //POST
    const PUBLICATION_ADD       = "/publication/add";
    //PUT
    const PUBLICATION_UPDATE    = "/publication/update";
    //PUT
    const PUBLICATION_DELETE    = "/publication/del";
    //GET
    const PUBLICATION_LIST      = "/publication/list";


    //POST
    const EXECUTING_UNIT_ADD    = "/executing_unit/add";
    //PUT
    const EXECUTING_UNIT_UPDATE = "/executing_unit/update";
    //POST
    const EXECUTING_UNIT_DEL    = "/executing_unit/del";
    //GET
    const EXECUTING_UNIT_LIST   = "/executing_unit/list";

    //POST
    const INSTITUTION_ADD    = "/institution/add";
    //PUT
    const INSTITUTION_UPDATE = "/institution/update";
    //PUT
    const INSTITUTION_DEL    = "/institution/del";
    //GET
    const INSTITUTION_LIST   = "/institution/list";

    //GET
    const CURRENCY_GET       = "/currency/get";

    //POST
    const PROJECT_ADD        = "/project/add";
    //PUT
    const PROJECT_UPDATE     = "/project/update";
    //PUT
    const PROJECT_DEL        = "/project/del";
    //GET
    const PROJECT_LIST       = "/project/list";


    //GET
    const STATUS_APPLICATION_LIST   = "/project/status_app/list";

    //GET
    const CURRENT_STATUS_LIST       = "/project/current_status/list";


    const REPORT_PROJECT_EARNINGS    = "/report/projects/earnings/:s";
    const REPORT_PROJECT_OVERHEAD    = "/report/projects/earnings/overhead/:s";
    const REPORT_PROJECT_TOTALAMOUNT = "/report/projects/earnings/total-amount-overhead/:s";
    const REPORT_PROJECT_QUANTITY    = "/report/projects/quantity/:s";
    const REPORT_PUBLICATIONS        = "/report/publications/:s";
    const REPORT_CONGRESS            = "/report/congress/:s";
    const REPORT_PROJECT             = "/report/projects/:s";
    const REPORT_ANNUAL              = "/report/annual/:s";
}

