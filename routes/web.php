<?php

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */

use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\EventAccessCodesController;
use App\Http\Controllers\EventAttendeesController;
use App\Http\Controllers\EventCheckInController;
use App\Http\Controllers\EventCheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventCustomizeController;
use App\Http\Controllers\EventDashboardController;
use App\Http\Controllers\EventOrdersController;
use App\Http\Controllers\EventPromoteController;
use App\Http\Controllers\EventRegistrationCategoryController;
use App\Http\Controllers\EventRegistrationConferenceController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\EventRegistrationProfessionController;
use App\Http\Controllers\EventSurveyController;
use App\Http\Controllers\EventTicketsController;
use App\Http\Controllers\EventUserTypeController;
use App\Http\Controllers\EventViewController;
use App\Http\Controllers\EventViewEmbeddedController;
use App\Http\Controllers\EventWidgetsController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\ManageAccountController;
use App\Http\Controllers\OrganiserController;
use App\Http\Controllers\OrganiserCustomizeController;
use App\Http\Controllers\OrganiserDashboardController;
use App\Http\Controllers\OrganiserEventsController;
use App\Http\Controllers\OrganiserViewController;
use App\Http\Controllers\RegistrationUsersController;
use App\Http\Controllers\RemindersController;
use App\Http\Controllers\TicketTemplateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserLogoutController;
use App\Http\Controllers\UserSignupController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        // 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        /*
         * -------------------------
         * Installer
         * -------------------------
         */
        Route::get(
            'install',
            [InstallerController::class, 'showInstaller']
        )->name('showInstaller');

        Route::post(
            'install',
            [InstallerController::class, 'postInstaller']
        )->name('postInstaller');

        Route::get(
            'upgrade',
            [InstallerController::class, 'showUpgrader']
        )->name('showUpgrader');

        Route::post(
            'upgrade',
            [InstallerController::class, 'postUpgrader']
        )->name('postUpgrader');

        /*
         * Logout
         */
        Route::any(
            '/logout',
            [UserLogoutController::class, 'doLogout']
        )->name('logout');

        Route::group(['middleware' => ['installed']], function () {
            /*
             * Login
             */
            Route::get(
                '/login',
                [UserLoginController::class, 'showLogin']
            )->name('login')->middleware('throttle:10,1');

            Route::post(
                '/login',
                [UserLoginController::class, 'postLogin']
            );

            /*
             * Forgot password
             */
            Route::get(
                'login/forgot-password',
                [RemindersController::class, 'getRemind']
            )->name('forgotPassword');

            Route::post(
                'login/forgot-password',
                [RemindersController::class, 'postRemind']
            )->name('postForgotPassword')->middleware('throttle:3,1');

            /*
             * Reset Password
             */
            Route::get(
                'login/reset-password/{token}',
                [RemindersController::class, 'getReset']
            )->name('password.reset');

            Route::post(
                'login/reset-password',
                [RemindersController::class, 'postReset']
            )->name('postResetPassword')->middleware('throttle:3,1');

            /*
             * Registration / Account creation
             */
            Route::get(
                '/signup',
                [UserSignupController::class, 'showSignup']
            )->name('showSignup');

            Route::post(
                '/signup',
                [UserSignupController::class, 'postSignup']
            )->middleware('throttle:3,1');

            /*
             * Confirm Email
             */
            Route::get(
                'signup/confirm_email/{confirmation_code}',
                [UserSignupController::class, 'confirmEmail']
            )->name('confirmEmail')->middleware('throttle:3,1');
        });

        /*
         * Public organiser page routes
         */
        Route::group(['prefix' => 'o'], function () {
            Route::get(
                '/{organiser_id}/{organier_slug?}',
                [OrganiserViewController::class, 'showOrganiserHome']
            )->name('showOrganiserHome');
        });

        /*
         * Public event page routes
         */
        Route::group(['prefix' => 'e'], function () {
            /*
             * Embedded events
             */
            Route::get(
                '/{event_id}/embed',
                [EventViewEmbeddedController::class, 'showEmbeddedEvent']
            )->name('showEmbeddedEventPage');

            Route::get(
                '/{event_id}/calendar.ics',
                [EventViewController::class, 'showCalendarIcs']
            )->name('downloadCalendarIcs');

            Route::get(
                '/{event_id}/{event_slug?}',
                [EventViewController::class, 'showEventHome']
            )->name('showEventPage');

            // Registration Form Routes
            Route::get(
                '/{event_id}/{event_slug}/registration/{registration_id}',
                [EventViewController::class, 'showEventRegistrationForm']
            )->name('showEventRegistrationForm');

            Route::post(
                '/{event_id}/registration/{registration_id}',
                [EventViewController::class, 'postEventRegistration']
            )->name('postEventRegistration');

            // API Route for Professions
            Route::get(
                '/api/conferences/{conference_id}/professions',
                [EventRegistrationProfessionController::class, 'getConferenceProfessions']
            );

            Route::get(
                '/api/categories/{category_id}/conferences',
                [EventRegistrationProfessionController::class, 'getCategoryConferences']
            );

            Route::post(
                '/{event_id}/contact_organiser',
                [EventViewController::class, 'postContactOrganiser']
            )->name('postContactOrganiser');

            Route::post(
                '/{event_id}/show_hidden',
                [EventViewController::class, 'postShowHiddenTickets']
            )->name('postShowHiddenTickets');

            Route::get(
                '/{event_id}/{event_slug?}/registration/{registration_id}',
                [EventRegistrationController::class, 'showRegistrationForm']
            )->name('showEventRegistrationForm');

            /*
             * Used for previewing designs in the backend. Doesn't log page views etc.
             */
            Route::get(
                '/{event_id}/preview',
                [EventViewController::class, 'showEventHomePreview']
            )->name('showEventPagePreview');

            Route::post(
                '{event_id}/checkout/',
                [EventCheckoutController::class, 'postValidateTickets']
            )->name('postValidateTickets');

            Route::post(
                '{event_id}/checkout/validate',
                [EventCheckoutController::class, 'postValidateOrder']
            )->name('postValidateOrder');

            Route::get(
                '{event_id}/checkout/payment',
                [EventCheckoutController::class, 'showEventPayment']
            )->name('showEventPayment');

            Route::get(
                '{event_id}/checkout/create',
                [EventCheckoutController::class, 'showEventCheckout']
            )->name('showEventCheckout');

            Route::get(
                '{event_id}/checkout/success',
                [EventCheckoutController::class, 'showEventCheckoutPaymentReturn']
            )->name('showEventCheckoutPaymentReturn');

            Route::post(
                '{event_id}/checkout/create',
                [EventCheckoutController::class, 'postCreateOrder']
            )->name('postCreateOrder');
        });

        /*
         * Public view order routes
         */
        Route::get(
            'order/{order_reference}',
            [EventCheckoutController::class, 'showOrderDetails']
        )->name('showOrderDetails');

        Route::get(
            'order/{order_reference}/tickets',
            [EventCheckoutController::class, 'showOrderTickets']
        )->name('showOrderTickets');

        /*
         * Backend routes
         */
        Route::group(['middleware' => ['auth', 'first.run']], function () {
            /*
             * Edit User
             */
            Route::group(['prefix' => 'user'], function () {
                Route::get(
                    '/',
                    [UserController::class, 'showEditUser']
                )->name('showEditUser');

                Route::post(
                    '/',
                    [UserController::class, 'postEditUser']
                )->name('postEditUser');
            });

            /*
             * Manage account
             */
            Route::group(['prefix' => 'account'], function () {
                Route::get(
                    '/',
                    [ManageAccountController::class, 'showEditAccount']
                )->name('showEditAccount');

                Route::post(
                    '/',
                    [ManageAccountController::class, 'postEditAccount']
                )->name('postEditAccount');

                Route::post(
                    '/edit_payment',
                    [ManageAccountController::class, 'postEditAccountPayment']
                )->name('postEditAccountPayment');

                Route::post(
                    'invite_user',
                    [ManageAccountController::class, 'postInviteUser']
                )->name('postInviteUser');
            });

            Route::get(
                'select_organiser',
                [OrganiserController::class, 'showSelectOrganiser']
            )->name('showSelectOrganiser');

            /*
             * Organiser routes
             */
            Route::group(['prefix' => 'organiser'], function () {
                Route::get(
                    '{organiser_id}/dashboard',
                    [OrganiserDashboardController::class, 'showDashboard']
                )->name('showOrganiserDashboard');

                Route::get(
                    '{organiser_id}/events',
                    [OrganiserEventsController::class, 'showEvents']
                )->name('showOrganiserEvents');

                Route::get(
                    '{organiser_id}/customize',
                    [OrganiserCustomizeController::class, 'showCustomize']
                )->name('showOrganiserCustomize');

                Route::post(
                    '{organiser_id}/customize',
                    [OrganiserCustomizeController::class, 'postEditOrganiser']
                )->name('postEditOrganiser');

                Route::get(
                    'create',
                    [OrganiserController::class, 'showCreateOrganiser']
                )->name('showCreateOrganiser');

                Route::post(
                    'create',
                    [OrganiserController::class, 'postCreateOrganiser']
                )->name('postCreateOrganiser');

                Route::post(
                    '{organiser_id}/page_design',
                    [OrganiserCustomizeController::class, 'postEditOrganiserPageDesign']
                )->name('postEditOrganiserPageDesign');
            });

            /*
             * Events dashboard
             */
            Route::group(['prefix' => 'events'], function () {
                /*
                 * ----------
                 * Create Event
                 * ----------
                 */
                Route::get(
                    '/create',
                    [EventController::class, 'showCreateEvent']
                )->name('showCreateEvent');

                Route::post(
                    '/create',
                    [EventController::class, 'postCreateEvent']
                )->name('postCreateEvent');
            });

            /*
             * Upload event images
             */
            Route::post(
                '/upload_image',
                [EventController::class, 'postUploadEventImage']
            )->name('postUploadEventImage');

            /*
             * Event management routes
             */
            Route::group(['prefix' => 'event'], function () {
                /*
                 * Dashboard
                 */
                Route::get(
                    '{event_id}/dashboard/',
                    [EventDashboardController::class, 'showDashboard']
                )->name('showEventDashboard');

                Route::get(
                    '{event_id}/',
                    [EventDashboardController::class, 'redirectToDashboard']
                );

                Route::post(
                    '{event_id}/go_live',
                    [EventController::class, 'postMakeEventLive']
                )->name('MakeEventLive');

                /*
                 * -------
                 * Registration
                 * -------
                 */
                Route::get(
                    '{event_id}/registrations',
                    [EventRegistrationController::class, 'showRegistration']
                )->name('showEventRegistration');

                Route::get(
                    '{event_id}/registrations/create',
                    [EventRegistrationController::class, 'showCreateRegistration']
                )->name('showCreateEventRegistration');

                Route::post(
                    '{event_id}/registrations/create',
                    [EventRegistrationController::class, 'postCreateRegistration']
                )->name('postCreateEventRegistration');

                Route::post(
                    '{event_id}/registrations/bulk-delete',
                    [EventRegistrationController::class, 'postBulkDeleteRegistrations']
                )->name('postBulkDeleteRegistrations');

                Route::get(
                    '/event/{event_id}/registrations/{registration_id}/details',
                    [EventRegistrationController::class, 'showRegistrationDetails']
                )->name('showEventRegistrationDetails');

                Route::get(
                    '/event/{event_id}/registrations/{registration_id}/edit',
                    [EventRegistrationController::class, 'showEditRegistration']
                )->name('showEditEventRegistration');

                Route::post(
                    '/event/{event_id}/registrations/{registration_id}/edit',
                    [EventRegistrationController::class, 'postEditRegistration']
                )->name('postEditRegistration');

                Route::get(
                    '/event/{event_id}/registrations/{registration_id}/delete',
                    [EventRegistrationController::class, 'showDeleteRegistration']
                )->name('showDeleteEventRegistration');

                Route::delete(
                    '/event/{event_id}/registrations/{registration_id}',
                    [EventRegistrationController::class, 'postDeleteRegistration']
                )->name('postDeleteRegistration');

                Route::post(
                    '/event/{event_id}/registrations/bulk-delete',
                    [EventRegistrationController::class, 'postBulkDeleteRegistrations']
                )->name('postBulkDeleteRegistrations');

                Route::get(
                    '/event/{event_id}/registrations/users',
                    [RegistrationUsersController::class, 'showEventRegistrationUsers']
                )->name('showEventRegistrationUsers');

                Route::get(
                    '/event/{event_id}/registrations/{registration_id}/users',
                    [RegistrationUsersController::class, 'showRegistrationUsers']
                )->name('showRegistrationUsers');

                Route::post(
                    '/event/{event_id}/registrations/users/{user_id}/status',
                    [RegistrationUsersController::class, 'updateUserStatus']
                )->name('updateUserStatus');

                Route::delete(
                    '/event/{event_id}/registrations/users/{user_id}',
                    [RegistrationUsersController::class, 'deleteUser']
                )->name('deleteUser');

                Route::post(
                    '/event/{event_id}/registrations/users/bulk',
                    [RegistrationUsersController::class, 'bulkUpdateUsers']
                )->name('bulkUpdateUsers');

                Route::get(
                    '/event/{event_id}/registrations/users/{user_id}/details',
                    [RegistrationUsersController::class, 'getUserDetails']
                )->name('getUserDetails');

                 // Add User
                Route::get('{event_id}/registration/users/add/{registration_id?}', [App\Http\Controllers\RegistrationUsersController::class, 'showAddUser'])
                    ->name('showAddUser');
                Route::post('{event_id}/registration/users/store', [App\Http\Controllers\RegistrationUsersController::class, 'storeUser'])
                    ->name('storeUser');

                // Edit User
                Route::get('{event_id}/registration/users/{user_id}/edit', [App\Http\Controllers\RegistrationUsersController::class, 'showEditUser'])
                    ->name('showEditUserResgistration');
                Route::post('{event_id}/registration/users/{user_id}/update', [App\Http\Controllers\RegistrationUsersController::class, 'updateUser'])
                    ->name('updateUser');

                // Import Users
                Route::get('{event_id}/registration/users/import', [App\Http\Controllers\RegistrationUsersController::class, 'showImportUsers'])
                    ->name('showImportUsers');
                Route::post('{event_id}/registration/users/import', [App\Http\Controllers\RegistrationUsersController::class, 'importUsers'])
                    ->name('importUsers');
                Route::get('{event_id}/registration/users/template', [App\Http\Controllers\RegistrationUsersController::class, 'downloadTemplate'])
                    ->name('downloadTemplate');

                Route::post('/manage/event/{event_id}/registrations/users/export-selected', [
                    'as' => 'exportSelectedUsers',
                    'uses' => 'RegistrationUsersController@exportSelectedUsers'
                ]);

                // Bulk email actions
                Route::post('{event_id}/users/bulk/send-approval-emails', [App\Http\Controllers\RegistrationUsersController::class, 'sendApprovalEmails'])
                    ->name('sendApprovalEmails');
                Route::post('{event_id}/users/bulk/send-rejection-emails', [App\Http\Controllers\RegistrationUsersController::class, 'sendRejectionEmails'])
                    ->name('sendRejectionEmails');

                // Single user email actions
                Route::post('{event_id}/users/{user_id}/send-approval-email', [App\Http\Controllers\RegistrationUsersController::class, 'sendApprovalEmail'])
                    ->name('sendApprovalEmail');
                Route::post('{event_id}/users/{user_id}/send-rejection-email', [App\Http\Controllers\RegistrationUsersController::class, 'sendRejectionEmail'])
                    ->name('sendRejectionEmail');

                // Custom email
                Route::get('{event_id}/users/{user_id}/custom-email', [App\Http\Controllers\RegistrationUsersController::class, 'showCustomEmail'])
                    ->name('showCustomEmail');
                Route::post('{event_id}/users/{user_id}/send-custom-email', [App\Http\Controllers\RegistrationUsersController::class, 'sendCustomEmail'])
                    ->name('sendCustomEmail');

                // AJAX Routes
                Route::get('{event_id}/registrations/{registration_id}/fields', [App\Http\Controllers\RegistrationUsersController::class, 'getRegistrationFields'])
                    ->name('getRegistrationFields');

                 Route::get('{event_id}/conferences/{conference_id}/professions', [App\Http\Controllers\RegistrationUsersController::class, 'getConferenceProfessions'])
                        ->name('getConferenceProfessions');

                Route::get('{event_id}/registration/user-types', [EventUserTypeController::class, 'showUserTypes'])
                    ->name('showEventUserTypes');

                Route::get('{event_id}/registration/user-types/create', [EventUserTypeController::class, 'showCreateUserType'])
                    ->name('showCreateEventUserType');

                Route::post('{event_id}/registration/user-types/create', [EventUserTypeController::class, 'postCreateUserType'])
                    ->name('postCreateEventUserType');

                Route::get('{event_id}/registration/user-types/{user_type_id}/edit', [EventUserTypeController::class, 'showEditUserType'])
                    ->name('showEditEventUserType');

                Route::post('{event_id}/registration/user-types/{user_type_id}/edit', [EventUserTypeController::class, 'postEditUserType'])
                    ->name('postEditEventUserType');

                Route::get('{event_id}/registration/user-types/{user_type_id}/delete', [EventUserTypeController::class, 'showDeleteUserType'])
                    ->name('showDeleteEventUserType');

                Route::post('{event_id}/registration/user-types/{user_type_id}/delete', [EventUserTypeController::class, 'postDeleteUserType'])
                    ->name('postDeleteEventUserType');

                Route::post('{event_id}/registration/user-types/bulk-delete', [EventUserTypeController::class, 'postBulkDeleteUserTypes'])
                    ->name('postBulkDeleteUserTypes');

                /*
                 * ----------
                 * Categories
                 * ----------
                 */
                Route::get(
                    '{event_id}/registration/categories',
                    [EventRegistrationCategoryController::class, 'showCategories']
                )->name('showEventRegistrationCategories');

                Route::get(
                    '{event_id}/registration/categories/create',
                    [EventRegistrationCategoryController::class, 'showCreateCategory']
                )->name('showCreateEventRegistrationCategory');

                Route::post(
                    '{event_id}/registration/categories/create',
                    [EventRegistrationCategoryController::class, 'postCreateCategory']
                )->name('postCreateEventRegistrationCategory');

                Route::get(
                    '{event_id}/registration/categories/{category_id}/edit',
                    [EventRegistrationCategoryController::class, 'showEditCategory']
                )->name('showEditEventRegistrationCategory');

                Route::post(
                    '{event_id}/registration/categories/{category_id}/edit',
                    [EventRegistrationCategoryController::class, 'postEditCategory']
                )->name('postEditEventRegistrationCategory');

                Route::get(
                    '{event_id}/registration/categories/{category_id}/delete',
                    [EventRegistrationCategoryController::class, 'showDeleteCategory']
                )->name('showDeleteEventRegistrationCategory');

                Route::post(
                    '{event_id}/registration/categories/{category_id}/delete',
                    [EventRegistrationCategoryController::class, 'postDeleteCategory']
                )->name('postDeleteEventRegistrationCategory');

                Route::post(
                    '{event_id}/registration/categories/bulk-delete',
                    [EventRegistrationCategoryController::class, 'postBulkDeleteCategories']
                )->name('postBulkDeleteCategories');

                /*
                 * ----------
                 * Conferences
                 * ----------
                 */
                Route::get(
                    '{event_id}/registration/conferences',
                    [EventRegistrationConferenceController::class, 'showConferences']
                )->name('showEventRegistrationConferences');

                Route::get(
                    '{event_id}/registration/conferences/create',
                    [EventRegistrationConferenceController::class, 'showCreateConferences']
                )->name('showCreateEventRegistrationConference');

                Route::post(
                    '{event_id}/registration/conferences/create',
                    [EventRegistrationConferenceController::class, 'postCreateConference']
                )->name('postCreateEventRegistrationConference');

                Route::get(
                    '{event_id}/registration/conferences/{conference_id}/edit',
                    [EventRegistrationConferenceController::class, 'showEditConference']
                )->name('showEditEventRegistrationConference');

                Route::post(
                    'registration/conferences/{conference_id}/edit',
                    [EventRegistrationConferenceController::class, 'postEditConference']
                )->name('postEditEventRegistrationConference');

                Route::get(
                    '{event_id}/registration/conferences/{conference_id}/delete',
                    [EventRegistrationConferenceController::class, 'showDeleteConference']
                )->name('showDeleteEventRegistrationConference');

                Route::post(
                    '{event_id}/registration/conferences/{conference_id}/delete',
                    [EventRegistrationConferenceController::class, 'postDeleteConference']
                )->name('postDeleteEventRegistrationConference');

                Route::post(
                    '{event_id}/registration/conferences/bulk-delete',
                    [EventRegistrationConferenceController::class, 'postBulkDeleteConferences']
                )->name('postBulkDeleteConferences');

                Route::get(
                    '{event_id}/registration/conferences/{conference_id}/categories',
                    [EventRegistrationConferenceController::class, 'showCategoriesConference']
                )->name('showEventRegistrationCategoriesConference');

                Route::get(
                    '{event_id}/registration/conferences/{conference_id}/professions',
                    [EventRegistrationConferenceController::class, 'showProfessionsConference']
                )->name('showEventRegistrationProfessionsConference');

                /*
                 * ----------
                 * Profession
                 * ----------
                 */
                Route::get(
                    '{event_id}/registration/professions/{profession_id}/edit',
                    [EventRegistrationProfessionController::class, 'showEditProfession']
                )->name('showEditEventRegistrationProfession');

                /*
                 * -------
                 * Tickets
                 * -------
                 */
                Route::get(
                    '{event_id}/tickets/',
                    [EventTicketsController::class, 'showTickets']
                )->name('showEventTickets');

                Route::get(
                    '{event_id}/tickets/edit/{ticket_id}',
                    [EventTicketsController::class, 'showEditTicket']
                )->name('showEditTicket');

                Route::post(
                    '{event_id}/tickets/edit/{ticket_id}',
                    [EventTicketsController::class, 'postEditTicket']
                )->name('postEditTicket');

                Route::get(
                    '{event_id}/tickets/create',
                    [EventTicketsController::class, 'showCreateTicket']
                )->name('showCreateTicket');

                Route::post(
                    '{event_id}/tickets/create',
                    [EventTicketsController::class, 'postCreateTicket']
                )->name('postCreateTicket');

                Route::post(
                    '{event_id}/tickets/delete',
                    [EventTicketsController::class, 'postDeleteTicket']
                )->name('postDeleteTicket');

                Route::post(
                    '{event_id}/tickets/pause',
                    [EventTicketsController::class, 'postPauseTicket']
                )->name('postPauseTicket');

                Route::post(
                    '{event_id}/tickets/order',
                    [EventTicketsController::class, 'postUpdateTicketsOrder']
                )->name('postUpdateTicketsOrder');

                /*
                 * -------
                 * Attendees
                 * -------
                 */
                Route::get(
                    '{event_id}/attendees/',
                    [EventAttendeesController::class, 'showAttendees']
                )->name('showEventAttendees');

                Route::get(
                    '{event_id}/attendees/message',
                    [EventAttendeesController::class, 'showMessageAttendees']
                )->name('showMessageAttendees');

                Route::post(
                    '{event_id}/attendees/message',
                    [EventAttendeesController::class, 'postMessageAttendees']
                )->name('postMessageAttendees');

                Route::get(
                    '{attendee_id}/attendees/single_message',
                    [EventAttendeesController::class, 'showMessageAttendee']
                )->name('showMessageAttendee');

                Route::post(
                    '{attendee_id}/attendees/single_message',
                    [EventAttendeesController::class, 'postMessageAttendee']
                )->name('postMessageAttendee');

                Route::get(
                    '{attendee_id}/attendees/resend_ticket',
                    [EventAttendeesController::class, 'showResendTicketToAttendee']
                )->name('showResendTicketToAttendee');

                Route::post(
                    '{attendee_id}/attendees/resend_ticket',
                    [EventAttendeesController::class, 'postResendTicketToAttendee']
                )->name('postResendTicketToAttendee');

                Route::get(
                    '{event_id}/attendees/invite',
                    [EventAttendeesController::class, 'showInviteAttendee']
                )->name('showInviteAttendee');

                Route::post(
                    '{event_id}/attendees/invite',
                    [EventAttendeesController::class, 'postInviteAttendee']
                )->name('postInviteAttendee');

                Route::get(
                    '{event_id}/attendees/import',
                    [EventAttendeesController::class, 'showImportAttendee']
                )->name('showImportAttendee');

                Route::post(
                    '{event_id}/attendees/import',
                    [EventAttendeesController::class, 'postImportAttendee']
                )->name('postImportAttendee');

                Route::get(
                    '{event_id}/attendees/print',
                    [EventAttendeesController::class, 'showPrintAttendees']
                )->name('showPrintAttendees');

                Route::get(
                    '{event_id}/attendees/{attendee_id}/export_ticket',
                    [EventAttendeesController::class, 'showExportTicket']
                )->name('showExportTicket');

                Route::get(
                    '{event_id}/attendees/{attendee_id}/ticket',
                    [EventAttendeesController::class, 'showAttendeeTicket']
                )->name('showAttendeeTicket');

                Route::get(
                    '{event_id}/attendees/export/{export_as?}',
                    [EventAttendeesController::class, 'showExportAttendees']
                )->name('showExportAttendees');

                Route::get(
                    '{event_id}/attendees/{attendee_id}/edit',
                    [EventAttendeesController::class, 'showEditAttendee']
                )->name('showEditAttendee');

                Route::post(
                    '{event_id}/attendees/{attendee_id}/edit',
                    [EventAttendeesController::class, 'postEditAttendee']
                )->name('postEditAttendee');

                Route::get(
                    '{event_id}/attendees/{attendee_id}/cancel',
                    [EventAttendeesController::class, 'showCancelAttendee']
                )->name('showCancelAttendee');

                Route::post(
                    '{event_id}/attendees/{attendee_id}/cancel',
                    [EventAttendeesController::class, 'postCancelAttendee']
                )->name('postCancelAttendee');

                /*
                 * -------
                 * Orders
                 * -------
                 */
                Route::get(
                    '{event_id}/orders/',
                    [EventOrdersController::class, 'showOrders']
                )->name('showEventOrders');

                Route::get(
                    'order/{order_id}',
                    [EventOrdersController::class, 'manageOrder']
                )->name('showManageOrder');

                Route::post(
                    'order/{order_id}/resend',
                    [EventOrdersController::class, 'resendOrder']
                )->name('resendOrder');

                Route::get(
                    'order/{order_id}/show/edit',
                    [EventOrdersController::class, 'showEditOrder']
                )->name('showEditOrder');

                Route::post(
                    'order/{order_id}/edit',
                    [EventOrdersController::class, 'postEditOrder']
                )->name('postOrderEdit');

                Route::get(
                    'order/{order_id}/cancel',
                    [EventOrdersController::class, 'showCancelOrder']
                )->name('showCancelOrder');

                Route::post(
                    'order/{order_id}/cancel',
                    [EventOrdersController::class, 'postCancelOrder']
                )->name('postCancelOrder');

                Route::post(
                    'order/{order_id}/mark_payment_received',
                    [EventOrdersController::class, 'postMarkPaymentReceived']
                )->name('postMarkPaymentReceived');

                Route::get(
                    '{event_id}/orders/export/{export_as?}',
                    [EventOrdersController::class, 'showExportOrders']
                )->name('showExportOrders');

                Route::get(
                    '{event_id}/orders/message/{order_id}',
                    [EventOrdersController::class, 'showMessageOrder']
                )->name('showMessageOrder');

                Route::post(
                    '{event_id}/orders/message/{order_id}',
                    [EventOrdersController::class, 'postMessageOrder']
                )->name('postMessageOrder');

                /*
                 * -------
                 * Edit Event
                 * -------
                 */
                Route::post(
                    '{event_id}/customize',
                    [EventController::class, 'postEditEvent']
                )->name('postEditEvent');

                /*
                 * -------
                 * Customize Design etc.
                 * -------
                 */
                Route::get(
                    '{event_id}/customize',
                    [EventCustomizeController::class, 'showCustomize']
                )->name('showEventCustomize');

                Route::get(
                    '{event_id}/customize/{tab?}',
                    [EventCustomizeController::class, 'showCustomize']
                )->name('showEventCustomizeTab');

                Route::post(
                    '{event_id}/customize/order_page',
                    [EventCustomizeController::class, 'postEditEventOrderPage']
                )->name('postEditEventOrderPage');

                Route::get(
                    '{event_id}/ticket_template',
                    [App\Http\Controllers\TicketTemplateController::class, 'showEventTicketTemplate']
                )->name('showEventTicketTemplate');

                Route::post(
                    '{event_id}/customize/ticket_template',
                    [App\Http\Controllers\TicketTemplateController::class, 'postEditEventTicketTemplate']
                )->name('postEditEventTicketTemplate');

                Route::post(
                    '{event_id}/ticket-template/save-positions',
                    [App\Http\Controllers\TicketTemplateController::class, 'savePositions']
                )->name('saveTicketPositions');

                Route::get('{event_id}/download-ticket/{user_id}', [App\Http\Controllers\TicketController::class, 'downloadUserTicket'])
                ->name('downloadUserTicket');

                Route::post(
                    '{event_id}/customize/design',
                    [EventCustomizeController::class, 'postEditEventDesign']
                )->name('postEditEventDesign');

                Route::post(
                    '{event_id}/customize/ticket_design',
                    [EventCustomizeController::class, 'postEditEventTicketDesign']
                )->name('postEditEventTicketDesign');

                Route::post(
                    '{event_id}/customize/social',
                    [EventCustomizeController::class, 'postEditEventSocial']
                )->name('postEditEventSocial');

                Route::post(
                    '{event_id}/customize/fees',
                    [EventCustomizeController::class, 'postEditEventFees']
                )->name('postEditEventFees');

                /*
                 * -------
                 * Event Widget page
                 * -------
                 */
                Route::get(
                    '{event_id}/widgets',
                    [EventWidgetsController::class, 'showEventWidgets']
                )->name('showEventWidgets');

                /*
                 * -------
                 * Event Access Codes page
                 * -------
                 */
                Route::get(
                    '{event_id}/access_codes',
                    [EventAccessCodesController::class, 'show']
                )->name('showEventAccessCodes');

                Route::get(
                    '{event_id}/access_codes/create',
                    [EventAccessCodesController::class, 'showCreate']
                )->name('showCreateEventAccessCode');

                Route::post(
                    '{event_id}/access_codes/create',
                    [EventAccessCodesController::class, 'postCreate']
                )->name('postCreateEventAccessCode');

                Route::post(
                    '{event_id}/access_codes/{access_code_id}/delete',
                    [EventAccessCodesController::class, 'postDelete']
                )->name('postDeleteEventAccessCode');

                /*
                 * -------
                 * Event Survey page
                 * -------
                 */
                Route::get(
                    '{event_id}/surveys',
                    [EventSurveyController::class, 'showEventSurveys']
                )->name('showEventSurveys');

                Route::get(
                    '{event_id}/question/create',
                    [EventSurveyController::class, 'showCreateEventQuestion']
                )->name('showCreateEventQuestion');

                Route::post(
                    '{event_id}/question/create',
                    [EventSurveyController::class, 'postCreateEventQuestion']
                )->name('postCreateEventQuestion');

                Route::get(
                    '{event_id}/question/{question_id}',
                    [EventSurveyController::class, 'showEditEventQuestion']
                )->name('showEditEventQuestion');

                Route::post(
                    '{event_id}/question/{question_id}',
                    [EventSurveyController::class, 'postEditEventQuestion']
                )->name('postEditEventQuestion');

                Route::post(
                    '{event_id}/question/delete/{question_id}',
                    [EventSurveyController::class, 'postDeleteEventQuestion']
                )->name('postDeleteEventQuestion');

                Route::get(
                    '{event_id}/question/{question_id}/answers',
                    [EventSurveyController::class, 'showEventQuestionAnswers']
                )->name('showEventQuestionAnswers');

                Route::post(
                    '{event_id}/questions/update_order',
                    [EventSurveyController::class, 'postUpdateQuestionsOrder']
                )->name('postUpdateQuestionsOrder');

                Route::get(
                    '{event_id}/answers/export/{export_as?}',
                    [EventSurveyController::class, 'showExportAnswers']
                )->name('showExportAnswers');

                Route::post(
                    '{event_id}/question/{question_id}/enable',
                    [EventSurveyController::class, 'postEnableQuestion']
                )->name('postEnableQuestion');

                /*
                 * -------
                 * Check In App
                 * -------
                 */
                Route::get(
                    '{event_id}/check_in',
                    [EventCheckInController::class, 'showCheckIn']
                )->name('showCheckIn');

                Route::post(
                    '{event_id}/check_in/search',
                    [EventCheckInController::class, 'postCheckInSearch']
                )->name('postCheckInSearch');

                Route::post(
                    '{event_id}/check_in/',
                    [EventCheckInController::class, 'postCheckInAttendee']
                )->name('postCheckInAttendee');

                Route::post(
                    '{event_id}/qrcode_check_in',
                    [EventCheckInController::class, 'postCheckInAttendeeQr']
                )->name('postQRCodeCheckInAttendee');

                Route::post(
                    '{event_id}/confirm_order_tickets/{order_id}',
                    [EventCheckInController::class, 'confirmOrderTicketsQr']
                )->name('confirmCheckInOrderTickets');

                /*
                 * -------
                 * Promote
                 * -------
                 */
                Route::get(
                    '{event_id}/promote',
                    [EventPromoteController::class, 'showPromote']
                )->name('showEventPromote');
            });
        });

        Route::get(
            '/',
            [IndexController::class, 'showIndex']
        )->name('index');
    }
);

// Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Language switcher route
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('language.switch');

// Your other routes
Route::get('/events/{event}', function () {
    return view('ViewEvent.show');
})->name('events.show');

// about us
Route::get('/about-us/{event}', function () {
    $event = Event::find(request('event'));
    return view('ViewEvent.about-us', compact('event'));
})->name('events.about-us');
Route::post('/contact-us/{event}', [ContactUsController::class, 'postContactUs'])->name('events.contact-us.post');
Route::get('/contact-us/{event}', [ContactUsController::class, 'index'])->name('events.contact-us');
Route::delete('/contact-us/{event}/delete/{message_id}', [ContactUsController::class, 'deleteMessage'])->name('events.contact-us.delete');
Route::delete('/contact-us/{event}/delete-selected', [ContactUsController::class, 'deleteSelectedMessages'])->name('events.contact-us.delete-selected');

// Public ticket routes
Route::get('/ticket/download/{token}', [App\Http\Controllers\TicketController::class, 'downloadTicket'])
    ->name('downloadTicket');
Route::get('/ticket/view/{token}', [App\Http\Controllers\TicketController::class, 'viewTicket'])
    ->name('viewTicket');

Route::get('/events/{event_id}/registration-confirmation', [EventViewController::class , 'showRegistrationConfirmation'])->name('showRegistrationConfirmation');

