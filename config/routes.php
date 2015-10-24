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

    //POST
    const  PARTICIPANTS_ADD = "/participants/add";
    //PUT
    const  PARTICIPANTS_UPDATE = "/participants/update";
    //GET
    const  PARTICIPANTS_LIST = "/participants/list";

    //POST
    const  CONGRESS_ADD = "/congress/add";
    //PUT
    const  CONGRESS_UPDATE = "/congress/update";
    //GET
    const  CONGRESS_LIST = "/congress/list";
    //end: Congress

}

