<?php
namespace Config;

class Routes{
    const USER_LOGIN = "/login";
    const USER_SUMMARY = "/summary";
    const USER_LOGOUT = "/logout";

    //Congress
    //POST
    const  SPONSOR_ADD = "/sponsor/add";
    //PUT
    const  SPONSOR_UPDATE = "/sponsor/update";
    //GET
    const  SPONSOR_LIST = "/sponsor/list";

    const  SPONSOR_DEL = "/sponsor/del";

    //POST
    const  PARTICIPANTS_ADD = "/participant/add";
    //PUT
    const  PARTICIPANTS_UPDATE = "/participant/update";
    //GET
    const  PARTICIPANTS_LIST = "/participant/list";

    //PUT
    const  PARTICIPANTS_DEL = "/participant/del";

    //POST
    const  CONGRESS_ADD = "/congress/add";
    //PUT
    const  CONGRESS_UPDATE = "/congress/update";
    //GET
    const  CONGRESS_LIST = "/congress/list";

    //PUT
    const  CONGRESS_DEL = "/congress/del";

    //end: Congress

}

