<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//custom routing
$route['index'] = "Home/index";
$route['site_visitors'] = "Home/siteVisitors";
$route['contact'] = "Contact/index";
$route['service_corner'] = "Home/service_corner";
$route['service_corner_details'] = "Home/service_corner_details";
$route['service_corner_details/(:num)'] = "Home/service_corner_details/$1";

$route['order-circular'] = "OrderCircular/index";
$route['order-circular/(:any)/?(:num)?'] = "OrderCircular/index";
$route['melakal/?(:num)?'] = "Download/forms";
$route['download/forms/?(:num)?'] = "Download/forms";
$route['download/act_rules/?(:num)?'] = "Download/download";
$route['download/softwares/?(:num)?'] = "Download/download";
$route['download/fonts/?(:num)?'] = "Download/download";
$route['download/academic_corner/?(:num)?'] = "Download/forms";

$route['notice_poster/?(:num)?'] = "Download/download";
$route['official_outlook/?(:num)?'] = "Download/download";

$route['office_bearer'] = "OfficeBearer/index";

$route['adayapaka_sabham'] = "AdayapakaSabham/index";

//$route['download'] = "Download/index";
//$route['download/forms'] = "Download/forms";
$route['gallery'] = "Gallery/index";
$route['gallery/?(:any)?'] = "Gallery/singleAlbum";
$route['news/?(:num)?'] = "News/index";

$route['district'] = "District/index";

$route['quicklink'] = "Quicklink/index";


$route['results'] = "Results/index";

$route['privacy-policy'] = "Privacy/index";



/* * ************************
 *                          *
 * Admin Bundle Routing     *
 *                          *
 * ************** ********* */
$route['admin'] = "admin/Authentication/login";
$route['admin/login'] = "admin/Authentication/login";
$route['admin/logout'] = "admin/Authentication/logout";
$route['admin/home'] = "admin/Home/index";
$route['admin/change_password'] = "admin/Home/changePassword";
$route['admin/flashnews/save'] = "admin/Home/flashNewsSave";

//User Management Page
$route['admin/aauth/users'] = "admin/AauthUsers/index";
$route['admin/aauth/add'] = "admin/AauthUsers/add";
$route['admin/aauth/create'] = "admin/AauthUsers/add";
$route['admin/aauth/edit'] = "admin/AauthUsers/edit";
$route['admin/aauth/update'] = "admin/AauthUsers/update";

$route['admin/aauth/group'] = "admin/AauthGroup/index";
$route['admin/aauth/group/add'] = "admin/AauthGroup/add";
$route['admin/aauth/group/edit/?(:num)'] = "admin/AauthGroup/edit";
$route['admin/aauth/group/update/?(:num)'] = "admin/AauthGroup/update";

$route['admin/aauth/group_to_menu'] = "admin/AauthGroupToMenu/index";
$route['admin/aauth/group_to_menu/add'] = "admin/AauthGroupToMenu/add";
$route['admin/aauth/group_to_menu/menu'] = "admin/AauthGroupToMenu/menu";
$route['admin/aauth/group_to_menu/edit/?(:num)'] = "admin/AauthGroupToMenu/edit";
$route['admin/aauth/group_to_menu/update/?(:num)'] = "admin/AauthGroupToMenu/update";

//Latest News Section
$route['admin/news/add'] = "admin/News/add";
$route['admin/news/edit/?(:num)?'] = "admin/News/edit";
$route['admin/news/update/?(:num)?'] = "admin/News/update";
$route['admin/news/delete/?(:num)?'] = "admin/News/delete";
$route['admin/news/publish'] = "admin/News/publish";
$route['admin/news/search/?(:num)?'] = "admin/News/search";
$route['admin/news/?(:num)?'] = "admin/News/index";
#####################################
//Orders and Circular section
//Add and edit category
$route['admin/order-circular/category'] = "admin/OrderCircular/category";
$route['admin/order-circular/category/add'] = "admin/OrderCircular/categoryAdd";
$route['admin/order-circular/category/edit/?(:num)?'] = "admin/OrderCircular/categoryEdit";
$route['admin/order-circular/category/update/?(:num)?'] = "admin/OrderCircular/categoryUpdate";

$route['admin/order-circular/(:any)/add'] = "admin/OrderCircular/add";
$route['admin/order-circular/(:any)/fileupload'] = "admin/OrderCircular/fileUpload";
$route['admin/order-circular/(:any)/fileremove'] = "admin/OrderCircular/fileRemove";
$route['admin/order-circular/(:any)/search/?(:num)?'] = "admin/OrderCircular/search";
$route['admin/order-circular/(:any)/?(:num)?'] = "admin/OrderCircular/index";
###################################
$route['admin/order-circular/(:any)/publish/?(:num)?'] = "admin/OrderCircular/publish";
$route['admin/order-circular/(:any)/edit/?(:num)?'] = "admin/OrderCircular/edit";
$route['admin/order-circular/(:any)/update/?(:num)?'] = "admin/OrderCircular/update";
$route['admin/order-circular/(:any)/delete/?(:num)?'] = "admin/OrderCircular/delete";
//#################################
//Gallery
$route['admin/gallery'] = "admin/Gallery/index";
$route['admin/gallery/add'] = "admin/Gallery/add";
$route['admin/gallery/edit/?(:num)?'] = "admin/Gallery/edit";
$route['admin/gallery/update/?(:num)?'] = "admin/Gallery/update";
$route['admin/gallery/?(:any)?'] = "admin/Gallery/singleGallery";
$route['admin/gallery/?(:any)?/upload'] = "admin/Gallery/singleGalleryUpload";
$route['admin/gallery/?(:any)?/makecover/?(:num)?'] = "admin/Gallery/makeAlbumCover";
$route['admin/gallery/?(:any)?/edit/?(:num)?'] = "admin/Gallery/imageEdit";
$route['admin/gallery/?(:any)?/update/?(:num)?'] = "admin/Gallery/imageUpdate";
$route['admin/gallery/?(:any)?/delete/?(:num)?'] = "admin/Gallery/imageDelete";
######################
#AdayapakaSabham
$route['admin/adayapaka_sabham/?(:num)?'] = "admin/AdayapakaSabham/index";
$route['admin/adayapaka_sabham/add'] = "admin/AdayapakaSabham/add";
$route['admin/adayapaka_sabham/edit/?(:num)?'] = "admin/AdayapakaSabham/edit";
$route['admin/adayapaka_sabham/update/?(:num)?'] = "admin/AdayapakaSabham/update";
$route['admin/adayapaka_sabham/publish/?(:num)?'] = "admin/AdayapakaSabham/publish";
$route['admin/adayapaka_sabham/delete/?(:num)?'] = "admin/AdayapakaSabham/delete";
//Download
$route['admin/download/category'] = "admin/Download/category";
$route['admin/download/category/add'] = "admin/Download/categoryAdd";
$route['admin/download/category/edit/?(:num)?'] = "admin/Download/categoryEdit";
$route['admin/download/category/update/?(:num)?'] = "admin/Download/categoryUpdate";

$route['admin/download/?(:any)?/add'] = "admin/Download/add";
$route['admin/download/?(:any)?/fileupload'] = "admin/Download/fileUpload";
$route['admin/download/?(:any)?/fileremove'] = "admin/Download/fileRemove";
$route['admin/download/?(:any)?/?(:num)?'] = "admin/Download/index";
$route['admin/download/?(:any)?/publish/?(:num)?'] = "admin/Download/publish";
$route['admin/download/?(:any)?/edit/?(:num)?'] = "admin/Download/edit";
$route['admin/download/?(:any)?/update/?(:num)?'] = "admin/Download/update";
$route['admin/download/?(:any)?/delete/?(:num)?'] = "admin/Download/delete";
//Notice and Posters
$route['admin/notice_poster/add'] = "admin/Download/add";
$route['admin/notice_poster/fileupload'] = "admin/Download/fileUpload";
$route['admin/notice_poster/fileremove'] = "admin/Download/fileRemove";
$route['admin/notice_poster/?(:num)?'] = "admin/Download/index";
$route['admin/notice_poster/publish/?(:num)?'] = "admin/Download/publish";
$route['admin/notice_poster/edit/?(:num)?'] = "admin/Download/edit";
$route['admin/notice_poster/update/?(:num)?'] = "admin/Download/update";
$route['admin/notice_poster/delete/?(:num)?'] = "admin/Download/delete";

//Melakal
$route['admin/melakal'] = "admin/Download/index";
$route['admin/melakal/category'] = "admin/Download/category";
$route['admin/melakal/category/add'] = "admin/Download/categoryAdd";
$route['admin/melakal/category/edit/?(:num)?'] = "admin/Download/categoryEdit";
$route['admin/melakal/category/update/?(:num)?'] = "admin/Download/categoryUpdate";
$route['admin/melakal/add'] = "admin/Download/add";
$route['admin/melakal/fileupload'] = "admin/Download/fileUpload";
$route['admin/melakal/fileremove'] = "admin/Download/fileRemove";
$route['admin/melakal/?(:num)?'] = "admin/Download/index";
$route['admin/melakal/publish/?(:num)?'] = "admin/Download/publish";
$route['admin/melakal/edit/?(:num)?'] = "admin/Download/edit";
$route['admin/melakal/update/?(:num)?'] = "admin/Download/update";
$route['admin/melakal/delete/?(:num)?'] = "admin/Download/delete";



//official outlook
$route['admin/official_outlook/add'] = "admin/Download/add";
$route['admin/official_outlook/fileupload'] = "admin/Download/fileUpload";
$route['admin/official_outlook/fileremove'] = "admin/Download/fileRemove";
$route['admin/official_outlook/?(:num)?'] = "admin/Download/index";
$route['admin/official_outlook/publish/?(:num)?'] = "admin/Download/publish";
$route['admin/official_outlook/edit/?(:num)?'] = "admin/Download/edit";
$route['admin/official_outlook/update/?(:num)?'] = "admin/Download/update";
$route['admin/official_outlook/delete/?(:num)?'] = "admin/Download/delete";


//Quick links
$route['admin/quicklink'] = "admin/Quicklink/index";
$route['admin/quicklink/add'] = "admin/Quicklink/add";
$route['admin/quicklink/edit/?(:num)?'] = "admin/Quicklink/edit";
$route['admin/quicklink/update/?(:num)?'] = "admin/Quicklink/update";
$route['admin/quicklink/delete/?(:num)?'] = "admin/Quicklink/delete";


//Result links
$route['admin/result_link'] = "admin/ResultLink/index";
$route['admin/result_link/add'] = "admin/ResultLink/add";
$route['admin/result_link/edit/?(:num)?'] = "admin/ResultLink/edit";
$route['admin/result_link/update/?(:num)?'] = "admin/ResultLink/update";
$route['admin/result_link/delete/?(:num)?'] = "admin/ResultLink/delete";


//office Bearers
$route['admin/office_bearer'] = "admin/OfficeBearer/index";
$route['admin/office_bearer/add'] = "admin/OfficeBearer/add";
$route['admin/office_bearer/edit/?(:num)?'] = "admin/OfficeBearer/edit";
$route['admin/office_bearer/update/?(:num)?'] = "admin/OfficeBearer/update";
$route['admin/office_bearer/publish/?(:num)?'] = "admin/OfficeBearer/publish";
$route['admin/office_bearer/delete/?(:num)?'] = "admin/OfficeBearer/delete";


//slider
$route['admin/slider'] = "admin/Slider/index";
$route['admin/slider/add'] = "admin/Slider/add";
$route['admin/slider/edit/?(:num)?'] = "admin/Slider/edit";
$route['admin/slider/update/?(:num)?'] = "admin/Slider/update";
$route['admin/slider/delete/?(:num)?'] = "admin/Slider/delete";


//Flash news
$route['admin/flash_news/publish/?(:num)?'] = "admin/FlashNews/publish";
$route['admin/flash_news/?(:any)?'] = "admin/FlashNews/index";
$route['admin/flash_news/?(:any)?/add'] = "admin/FlashNews/add";
$route['admin/flash_news/?(:any)?/edit/?(:num)?'] = "admin/FlashNews/edit";
$route['admin/flash_news/?(:any)?/update/?(:num)?'] = "admin/FlashNews/update";
$route['admin/flash_news/?(:any)?/delete/?(:num)?'] = "admin/FlashNews/delete";

//District
$route['admin/district'] = "admin/District/index";
$route['admin/district/add'] = "admin/District/add";
$route['admin/district/edit/?(:num)?'] = "admin/District/edit";
$route['admin/district/update/?(:num)?'] = "admin/District/update";
$route['admin/district/publish/?(:num)?'] = "admin/District/publish";

$route['admin/district/?(:any)?'] = "admin/District/district";
$route['admin/district/?(:num)?/add'] = "admin/District/addDistrictOfficeBearer";
$route['admin/district/?(:num)?/edit/?(:num)?'] = "admin/District/editDistrictOfficeBearer";
$route['admin/district/?(:num)?/update/?(:num)?'] = "admin/District/updateDistrictOfficeBearer";
$route['admin/district/?(:num)?/publish/?(:num)?'] = "admin/District/publishDistrictOfficeBearer";
$route['admin/district/?(:num)?/delete/?(:num)?'] = "admin/District/deleteDistrictOfficeBearer";


//membership
//$route['admin/membership'] = "admin/Membership/index";
//$route['admin/membership/add'] = "admin/Membership/add";
//$route['admin/membership/edit/?(:num)?'] = "admin/Membership/edit";
//$route['admin/membership/update/?(:num)?'] = "admin/Membership/update";
//$route['admin/membership/delete/?(:num)?'] = "admin/Membership/delete";
//$route['admin/membership/publish/?(:num)?'] = "admin/Membership/publish";
//$route['admin/membership/fileremove/?(:num)?'] = "admin/Membership/fileRemove";
//$route['admin/membership/fileupload/?(:num)?'] = "admin/Membership/fileUpload";
//Reaction gallery
$route['admin/reaction_gallery'] = "admin/ReactionGallery/index";
$route['admin/reaction_gallery/add'] = "admin/ReactionGallery/add";
$route['admin/reaction_gallery/edit/?(:num)?'] = "admin/ReactionGallery/edit";
$route['admin/reaction_gallery/update/?(:num)?'] = "admin/ReactionGallery/update";
$route['admin/reaction_gallery/delete/?(:num)?'] = "admin/ReactionGallery/delete";

$route['admin/backup'] = "admin/Backup";
$route['admin/backup/download_db'] = "admin/Backup/downloadDB";
$route['admin/backup/kpsta_db'] = "admin/Backup/kpstaDB";
$route['admin/backup/website'] = "admin/Backup/website";

//Web services
$route['webservice/register_token'] = "WebService/registerToken";



/* * *******************
 * 
 * Membership Bundle
 * 
 * ***************** */
$route['membership'] = "membership/Authentication/login";
$route['membership/login'] = "membership/Authentication/login";
$route['membership/logout'] = "membership/Authentication/logout";
$route['membership/home'] = "membership/Home/index";
$route['membership/home/membershipcount'] = "membership/Home/membershipcount";


//Teacher 
$route['membership/teacher'] = "membership/Teacher/index";
$route['membership/teacher/add'] = "membership/Teacher/add";
$route['membership/teacher/add_bulk'] = "membership/Teacher/addBulk";
$route['membership/teacher/edit/?(:num)?'] = "membership/Teacher/edit";
$route['membership/teacher/update/?(:num)?'] = "membership/Teacher/update";
$route['membership/teacher/delete/?(:num)?'] = "membership/Teacher/delete";
$route['membership/teacher/view/?(:num)?'] = "membership/Teacher/view";

//Report
$route['membership/teacher/generate_pdf'] = "membership/Teacher/generatePdf";
$route['membership/teacher/process'] = "membership/Teacher/process";
$route['membership/teacher/consoliated'] = "membership/Teacher/consoliated";
$route['membership/teacher/consolidation_report'] = "membership/Teacher/consolidationReport";
$route['membership/teacher/designation_report'] = "membership/Teacher/designationWiseReport";


//User Management Page
$route['membership/aauth/users'] = "membership/AauthUsers/index";
$route['membership/aauth/add'] = "membership/AauthUsers/add";
$route['membership/aauth/edit'] = "membership/AauthUsers/edit";
$route['membership/aauth/update/?(:num)?'] = "membership/AauthUsers/update";
$route['membership/aauth/getOffice'] = "membership/AauthUsers/getOffice";
$route['membership/aauth/username'] = "membership/AauthUsers/generateUserName";


//Main 
$route['membership/main'] = "membership/Main/index";
$route['membership/main/add'] = "membership/Main/add";
$route['membership/main/edit/?(:num)?'] = "membership/Main/edit";
$route['membership/main/update/?(:num)?'] = "membership/Main/update";
$route['membership/main/delete/?(:num)?'] = "membership/Main/delete";

//settings
$route['membership/settings/change_password'] = "membership/Home/changePassword";
$route['membership/settings/config'] = "membership/Home/config";

$route['membership/settings/whats_new'] = "membership/WhatsNew";
$route['membership/whats_new/add'] = "membership/WhatsNew/add";
$route['membership/whats_new/edit/?(:num)?'] = "membership/WhatsNew/edit";
$route['membership/whats_new/update/?(:num)?'] = "membership/WhatsNew/update";
$route['membership/whats_new/publish/?(:num)?'] = "membership/WhatsNew/publish";
$route['membership/whats_new/delete/?(:num)?'] = "membership/WhatsNew/delete";
$route['membership/whats_new/fileremove/?(:num)?'] = "membership/WhatsNew/fileRemove";

//donation

$route['donation'] = "Donation/index";
$route['donation/pay'] = "Donation/pay";
$route['donation/payment-status'] = "Donation/paymentStatus";
$route['donation/success/(:any)'] = "Donation/success";
