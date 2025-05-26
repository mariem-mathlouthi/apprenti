<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\studentController;
use App\Http\Controllers\entrepriseController;
use App\Http\Controllers\demandeController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\offreController;
use App\Http\Controllers\notificationController;
use App\Http\Controllers\attestationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\CoursSubscriptionsController;
use App\Http\Controllers\RessourceController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\QuizzController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\DomaineController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\Notifications2Controller;
use App\Http\Controllers\ReponseFeedbackController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\TypeStageController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/singupEtudiant', [authController::class, 'signUpEtudiant']);
Route::post('/signupEntreprise', [authController::class, 'signUpEntreprise']);
Route::post('/signupTuteur', [authController::class, 'signUpTuteur']);
Route::post('/admin', [adminController::class, 'signUpAdmin']); // For admin registration
Route::post('/login', [authController::class, 'LoginUser']);
Route::post('/forgot-password', [authController::class, 'forgot_password']);
Route::post('/reset-password', [authController::class, 'changer_password']);

Route::get('/secteurs', [SecteurController::class, 'getAllSecteurs']);
Route::get('/niveaux', [NiveauController::class, 'getAllNiveaux']);
Route::get('/domaines', [DomaineController::class, 'getAllDomaines']);
Route::get('/specialites', [SpecialiteController::class, 'getAllSpecialites']);



// Authenticated routes
// All routes within this group will require sanctum authentication and have CORS middleware applied
Route::middleware(['auth:sanctum', 'cors'])->group(function () {
    // User route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Chat routes
    Route::prefix('chat')->group(function () {
        Route::post('/student/send', [ChatController::class, 'sendMessageToTuteur']);
        Route::get('/etudiant/messages/{tutor_id}', [ChatController::class, 'getStudentMessages']);
        Route::post('/tutor/send', [ChatController::class, 'sendMessageToEtudiant']);
        Route::get('/tuteur/messages/{student_id}', [ChatController::class, 'getTutorMessages']);
        Route::post('/mark-read', [ChatController::class, 'markAsRead']);
        Route::get('/unread-count/{role}', [ChatController::class, 'getUnreadCount']);
        Route::get('/contacts/{role}', [ChatController::class, 'getContacts']);
    });

    // Appointment routes
    Route::post('/appointCall', [AppointmentController::class, 'create']);
    Route::get('/appointsCall', [AppointmentController::class, 'getAllAppointments']);
    Route::put('/appointsCall/{id}', [AppointmentController::class, 'updateAppointment']);
    Route::delete('/appointsCall/{id}', [AppointmentController::class, 'deleteAppointment']);
    Route::get('/appointByStudent/{id}', [AppointmentController::class, 'getAppointmentsByStudentId']);
    Route::get('/appointByTuteur/{id}', [AppointmentController::class, 'getAppointmentsByTuteurId']);

    // ReponseFeedback routes
    Route::put('reponse/{id}', [ReponseFeedbackController::class, 'updateReponse']);
    Route::delete('reponse/{id}', [ReponseFeedbackController::class, 'deleteReponse']);
    Route::post('feedbacks/{id}/reponse', [ReponseFeedbackController::class, 'reponseFeedback']);
    Route::get('feedback/{id}/reponses', [ReponseFeedbackController::class, 'getReponsesByFeedback']);

    // Notifications2 routes
    // Route::post('/send-notification', [Notifications2Controller::class, 'sendNotification']);
    // Route::get('/notifications', [Notifications2Controller::class, 'getNotificationsByUserId']);

    // CoursSubscriptions routes
    Route::get('/etudiants', [CoursSubscriptionsController::class, 'getAllStudentsSubscriptedCours']);
    Route::get('subscribtions/cours/{id}', [CoursSubscriptionsController::class, 'getIsStudentSubscribedToCourse']);
    Route::get('/subcsriptWithTueur', [CoursSubscriptionsController::class, 'getAllTutorsSubscripted']);
    Route::get('/subcsriptionEtudiant', [CoursSubscriptionsController::class, 'getAllStudentsSubscripted']);
    
    // Cours routes
    Route::post('/subscribeToCourse', [CoursController::class, 'subscribeToCourse']);
    Route::get('/cours', [CoursController::class, 'getAllCourses']);         
    Route::post('/cours', [CoursController::class, 'createCourse']);         
    Route::get('/cours/{id}', [CoursController::class, 'getCourseById']);    
    Route::put('/cours/{id}', [CoursController::class, 'updateCourse']);     
    Route::delete('/cours/{id}', [CoursController::class, 'deleteCourse']); 
    Route::get('/cours-by-tuteur', [CoursController::class, 'getAllCoursesByTuteur']);
    Route::get('/average-feedback/{courseId}', [CoursController::class, 'getAverageFeedback']);

    // Student routes
    Route::post('/modifyStudent', [studentController::class, 'ModifyEtudiantInfo']);
    Route::get('/getStudentDetail/{id}', [studentController::class, 'getStudentDetail']);
    Route::get('/studentsAdmin', [adminController::class, 'getAllStudents']); // Admin getting students

    // Entreprise routes
    Route::post('/modifyEntreprise', [entrepriseController::class, 'ModifyEntrepriseInfo']);
    Route::get('/getEntreprise/{idEntreprise}', [entrepriseController::class, 'getEntreprise']);
    Route::get('/enterprisesAdmin', [adminController::class, 'getAllEnterprises']); // Admin getting enterprises
    Route::delete('/enterprisesAdmin/{id}', [adminController::class, 'deleteEntreprise']); // Admin deleting enterprise

    // Demande routes
    Route::post('/updateSatutDemandeaddDemande', [demandeController::class, 'addDemande']);
    Route::post('/updateStatut/{id}', [demandeController::class, 'updateStatut']);
    Route::delete('/deleteDemande/{id}', [demandeController::class, 'deleteDemande']);
    Route::get('/getDemandes/{idEtudiant}', [demandeController::class, 'getAllDemandes']);
    Route::get('/getDemandeById/{id}', [demandeController::class, 'getDemandeById']);
    Route::get('/getDemandeByOfferId/{offerId}', [demandeController::class, 'getDemandeByOfferId']);
    Route::post('/addDemande', [demandeController::class, 'addDemande']);
    Route::post('/updateSatutDemande/{id}', [demandeController::class, 'updateStatut']);
    Route::get('/Demandes', [demandeController::class, 'Demandes']);

    // Offre routes
    Route::post('/addOffre', [offreController::class, 'addOffre']);
    Route::get('/getOffres/{idEntreprise}', [offreController::class, 'getAllOffres']);
    Route::get('/allOffres', [offreController::class, 'AllOffres']);
    Route::get('/offreDetail/{idEntreprise}/{id}', [offreController::class, 'getOffreDetail']);
    Route::get('/offreDetail2/{id}', [offreController::class, 'OffreDetail']);
    Route::post('/updateOffre', [offreController::class, 'updateOffre']);
    Route::post('/deleteOffre', [offreController::class, 'deleteOffre']);
    Route::get('/getAllOffreAdmin', [adminController::class, 'getAllOffresAdmin']); // Admin getting offers
    Route::post('/updateOfferStatus/{id}', [adminController::class, 'updateOfferStatus']); // Admin updating offer status
    Route::delete('/deleteOffer/{id}', [adminController::class, 'deleteOffer']); // Admin deleting offer

    // Tuteur routes (managed by Admin and Tuteur themselves)
    Route::get('/tuteurs', [AdminController::class, 'getAllTuteurs']); // Admin gets all tuteurs
    Route::put('/tuteurs/{id}/status', [AdminController::class, 'updateTuteurStatus']); // Admin updates tuteur status
    Route::delete('/tuteurs/{id}', [AdminController::class, 'deleteTuteur']); // Admin deletes tuteur
    Route::post('/tuteurs', [AdminController::class, 'addTuteur']); // Admin adds tuteur (distinct from signup)
    Route::get('/tuteurs/{id}', [TuteurController::class, 'getTuteurDetail']); // Tuteur gets own detail
    Route::post('/modifyTuteur', [TuteurController::class, 'ModifyTuteurInfo']); // Tuteur modifies own info
    Route::get('/getTuteurDetail/{id}', [TuteurController::class, 'getTuteurDetail']);
    Route::get('/pending-tuteurs', [adminController::class, 'getPendingTuteurs']); // Admin gets pending tuteurs
    Route::put('/tuteur/{id}/status', [adminController::class, 'updateTuteurStatus']); // Admin updates tuteur status (path diff from above)
    Route::post('/admin/tuteur', [AdminController::class, 'addTuteur']); // Admin adds tuteur (path diff from above)

    // Notification (old system) routes
    Route::post('/notification', [notificationController::class, 'notification']);
    Route::get('/getAllNotifications', [notificationController::class, 'getAllNotifications']);
    Route::put('/notification/{id}/visibility', [notificationController::class, 'updateNotificationVisibility']);

    // Attestation routes
    Route::post('/attestation', [attestationController::class, 'addAttestation']);
    Route::get('/getAttestation/{idEtudiant}', [attestationController::class, 'getAttestationByEtudiant_Offer']);

    // Admin general routes
    Route::get('/states', [adminController::class,'states']);
    
    // Category routes
    Route::get('/categories', [CategoryController::class, 'getAllCategories']);
    Route::post('/categories', [CategoryController::class, 'createCategory']);
    Route::get('/categories/{id}', [CategoryController::class, 'getCategoryById']);
    Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);

    // Ressource routes
    Route::get('ressources/cours/{idCours}', [RessourceController::class, 'getRessourcesByCours']);
    Route::get('/ressources', [RessourceController::class, 'getAllRessources']);         
    Route::post('/ressources', [RessourceController::class, 'createRessource']);         
    Route::get('/ressources/{id}', [RessourceController::class, 'getRessourceById']);    
    Route::put('/ressources/{id}', [RessourceController::class, 'updateRessource']);     
    Route::delete('/ressources/{id}', [RessourceController::class, 'deleteRessource']);
    Route::post('/upload', [RessourceController::class, 'uploadFile']);

    // Quizz routes
    Route::get('/quizz', [QuizzController::class, 'getAllQuizz']);
    Route::post('/quizz', [QuizzController::class, 'addQuizz']);
    Route::get('/quizz/{id}', [QuizzController::class, 'getQuizzById']);
    Route::put('/quizz/{id}', [QuizzController::class, 'updateQuizz']);
    Route::delete('/quizz/{id}', [QuizzController::class, 'deleteQuizz']);
    Route::get('/quizz-by-tuteur', [QuizzController::class, 'getQuizzByTuteur']);
    Route::get('/quizz/{idCours}/{titre}', [QuizzController::class, 'getByCourseAndTitle']);

    // Feedback routes
    Route::get('/feedbacks/{id}', [FeedbackController::class, 'getFeedback']);
    Route::get('/feedbacks/course/{courseId}', [FeedbackController::class, 'getFeedbacksByCourse']);
    Route::post('/feedbacks', [FeedbackController::class, 'createFeedback']);
    Route::delete('/feedbacks/{id}', [FeedbackController::class, 'deleteFeedback']);
    Route::put('/feedbacks/{id}', [FeedbackController::class, 'updateFeedback']);

    // Specialite routes
    Route::post('/specialites', [SpecialiteController::class, 'createSpecialite']);
    Route::get('/specialites/{id}', [SpecialiteController::class, 'getSpecialiteById']);
    Route::put('/specialites/{id}', [SpecialiteController::class, 'updateSpecialite']);
    Route::delete('/specialites/{id}', [SpecialiteController::class, 'deleteSpecialite']);

    // Domaine routes
    Route::post('/domaines', [DomaineController::class, 'createDomaine']);
    Route::get('/domaines/{id}', [DomaineController::class, 'getDomaineById']);
    Route::put('/domaines/{id}', [DomaineController::class, 'updateDomaine']);
    Route::delete('/domaines/{id}', [DomaineController::class, 'deleteDomaine']);

    // Niveau routes
    Route::post('/niveaux', [NiveauController::class, 'createNiveau']);
    Route::get('/niveaux/{id}', [NiveauController::class, 'getNiveauById']);
    Route::put('/niveaux/{id}', [NiveauController::class, 'updateNiveau']);
    Route::delete('/niveaux/{id}', [NiveauController::class, 'deleteNiveau']);

    // Secteur routes
    Route::post('/secteurs', [SecteurController::class, 'createSecteur']);
    Route::get('/secteurs/{id}', [SecteurController::class, 'getSecteurById']);
    Route::put('/secteurs/{id}', [SecteurController::class, 'updateSecteur']);
    Route::delete('/secteurs/{id}', [SecteurController::class, 'deleteSecteur']);

    // TypeStage routes
    Route::get('/type-stages', [TypeStageController::class, 'getAllTypeStages']);
    Route::post('/type-stages', [TypeStageController::class, 'createTypeStage']);
    Route::get('/type-stages/{id}', [TypeStageController::class, 'getTypeStageById']);
    Route::put('/type-stages/{id}', [TypeStageController::class, 'updateTypeStage']);
    Route::delete('/type-stages/{id}', [TypeStageController::class, 'deleteTypeStage']);
});


