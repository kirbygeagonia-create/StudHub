PS C:\Users\ADMIN> cd C:\Users\ADMIN\OneDrive\Desktop\Another_Project                          
PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> php artisan test --testsuite=Feature
                                                                  
   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                                                                                                                                  8.78s  
  ✓ users can authenticate using the login screen                                                                                                                                                 2.63s  
  ✓ users can not authenticate with invalid password                                                                                                                                              0.47s  
  ✓ users can logout                                                                                                                                                                              0.14s  

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                                                                                                                                     0.55s  
  ✓ email can be verified                                                                                                                                                                         0.37s  
  ✓ email is not verified with invalid hash                                                                                                                                                       0.39s  

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                                                                                                                                                       0.68s  
  ✓ password can be confirmed                                                                                                                                                                     0.13s  
  ✓ password is not confirmed with invalid password                                                                                                                                               0.35s  

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                                                                                                                                    0.68s  
  ✓ reset password link can be requested                                                                                                                                                          1.49s  
  ✓ reset password screen can be rendered                                                                                                                                                         1.66s  
  ✓ password can be reset with valid token                                                                                                                                                        0.73s  

   PASS  Tests\Feature\Auth\PasswordUpdateTest
  ✓ password can be updated                                                                                                                                                                       0.19s  
  ✓ correct password must be provided to update password                                                                                                                                          0.22s  

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                                                                                                                                                           1.76s  
  ✓ new users can register with a SEAIT email and land on onboarding                                                                                                                              7.62s  
  ✓ registration rejects email domains outside the allow-list                                                                                                                                     0.21s  

   PASS  Tests\Feature\Catalog\CreateResourceTest
  ✓ it creates a resource and stamps it with owner / school / program from the user                                                                                                               1.31s  
  ✓ it stores an uploaded file on the public disk and queues the watermark job                                                                                                                    0.64s  
  ✓ it does not queue the watermark job when no file is attached                                                                                                                                  0.30s  
  ✓ it refuses to create a resource for a subject in another school                                                                                                                               0.27s  
  ✓ it flips is_watermarked when the watermark job is run synchronously                                                                                                                           1.27s  

   PASS  Tests\Feature\Catalog\ResourceDownloadTest
  ✓ it downloads the original file for non-PDF resources                                                                                                                                          2.32s  
  ✓ it serves the download route for authenticated users                                                                                                                                          0.31s  
  ✓ it returns 404 for resources without a file                                                                                                                                                   0.38s  
  ✓ it redirects unauthenticated users from download route                                                                                                                                        0.35s  
  ✓ it increments save_count when a resource is saved via the route                                                                                                                               0.40s  

   PASS  Tests\Feature\Catalog\ResourceFormLivewireTest
  ✓ it rejects an empty title                                                                                                                                                                     2.76s  
  ✓ it creates a resource via the Livewire form and queues the watermark job                                                                                                                      0.78s  
  ✓ it rejects a file with a disallowed mimetype                                                                                                                                                  0.47s  

   PASS  Tests\Feature\Catalog\ResourceRoutesTest
  ✓ it renders the resources index for an onboarded user                                                                                                                                          6.42s  
  ✓ it renders the resource create form                                                                                                                                                           0.92s  
  ✓ it renders a resource detail page when accessible                                                                                                                                             1.14s  
  ✓ it blocks cross-program access for program_only resources                                                                                                                                     0.40s  
  ✓ it returns 404 when the resource belongs to a different school                                                                                                                                0.27s  
  ✓ it redirects unauthenticated users away from resources                                                                                                                                        0.25s  

   PASS  Tests\Feature\Catalog\SeaitSubjectsSeederTest
  ✓ it seeds the GE core subjects shared across the pilot trio                                                                                                                                    0.37s  
  ✓ it seeds at least one subject for each pilot program                                                                                                                                          0.42s  
  ✓ it persists subject aliases for student shorthand                                                                                                                                             0.26s  
  ✓ it is idempotent — running the subjects seeder twice produces stable counts                                                                                                                   0.40s  
  ✓ it records typical year level and weight on program_subjects rows                                                                                                                             0.34s  

   PASS  Tests\Feature\Catalog\SearchResourcesTest
  ✓ it lists only resources from the viewer's school                                                                                                                                              0.35s  
  ✓ it hides program_only resources from outside the program                                                                                                                                      0.32s  
  ✓ it filters by subject, type, program, and year                                                                                                                                                0.39s  
  ✓ it searches across title and description (LIKE on sqlite)                                                                                                                                     0.35s  
  ✓ it excludes archived and soft-deleted resources from search                                                                                                                                   0.24s  

   PASS  Tests\Feature\Catalog\ShelfTest
  ✓ it creates a default shelf on first save and adds the resource                                                                                                                                0.27s  
  ✓ it toggles a resource off the shelf and decrements save_count                                                                                                                                 0.24s  
  ✓ it reports isSaved correctly                                                                                                                                                                  0.24s  
  ✓ it reuses the existing shelf on subsequent saves                                                                                                                                              0.24s  
  ✓ it renders the shelf page for an onboarded user                                                                                                                                               0.65s  
  ✓ it shows saved resources on the shelf page                                                                                                                                                    0.85s  
  ✓ it toggle-save route saves a resource and redirects back                                                                                                                                      0.35s  

   PASS  Tests\Feature\Chat\ChatAccessTest
  ✓ it lists only rooms scoped to the user's program and year                                                                                                                                     1.08s  
  ✓ it lets a student open their own program room                                                                                                                                                 1.94s  
  ✓ it forbids a student from opening another program's room                                                                                                                                      0.34s  
  ✓ it forbids a student from opening a wrong-year sub-channel                                                                                                                                    0.34s  
  ✓ it redirects unauthenticated users to login                                                                                                                                                   0.37s  
  ✓ it does not pollute college reference                                                                                                                                                         0.27s  
  ✓ it suspended user is forbidden from chat listing                                                                                                                                              0.37s  
  ✓ it suspended user is forbidden from opening a chat room                                                                                                                                       0.26s  
  ✓ it expired suspension allows chat access                                                                                                                                                      0.27s  
  ✓ it prevents multiple @mention of same user                                                                                                                                                    0.40s  
  ✓ it rejects attachment with disallowed MIME type                                                                                                                                               0.27s  

   PASS  Tests\Feature\Chat\PostChatMessageTest
  ✓ it persists the message and dispatches the broadcast event                                                                                                                                    0.34s  
  ✓ it refuses to post an empty message without an attachment                                                                                                                                     0.28s  
  ✓ it resolves @display_name mentions and sends a database notification                                                                                                                          0.25s  
  ✓ it does not mention the sender themselves                                                                                                                                                     0.27s  
  ✓ it broadcasts on the chat-room scoped private channel                                                                                                                                         0.26s  

   PASS  Tests\Feature\Chat\ProvisionChatRoomsTest
  ✓ it creates one program room plus year sub-rooms for every active program                                                                                                                      0.25s  
  ✓ it is idempotent — running twice does not duplicate rooms                                                                                                                                     0.29s  
  ✓ it uses program slugs that include the program code                                                                                                                                           0.26s  

   PASS  Tests\Feature\Chat\RoomConversationLivewireTest
  ✓ it renders the composer with the empty-state message                                                                                                                                          0.34s  
  ✓ it persists a message via the Livewire component                                                                                                                                              0.37s  
  ✓ it rejects empty submissions                                                                                                                                                                  0.29s  

   PASS  Tests\Feature\ExampleTest
  ✓ it returns a successful response                                                                                                                                                              0.63s  

   PASS  Tests\Feature\Feedback\FeedbackTest
  ✓ it renders the feedback form for authenticated users                                                                                                                                          0.61s  
  ✓ it stores feedback as a bug report                                                                                                                                                            0.31s  
  ✓ it stores feedback as a feature request                                                                                                                                                       0.31s  
  ✓ it defaults type to feedback when no type is provided                                                                                                                                         0.26s  
  ✓ it rejects feedback shorter than 5 characters                                                                                                                                                 0.26s  
  ✓ it rejects feedback longer than 2000 characters                                                                                                                                               0.25s  
  ✓ it flashes a thank-you message on success                                                                                                                                                     0.26s  
  ✓ it blocks unauthenticated users from the feedback form                                                                                                                                        0.25s  
  ✓ it blocks unauthenticated users from submitting feedback                                                                                                                                      0.25s  

   PASS  Tests\Feature\Identity\AllowedEmailDomainTest
  ✓ it accepts emails from configured domains                                                                                                                                                     0.15s  
  ✓ it rejects emails outside the allow-list                                                                                                                                                      0.14s  
  ✓ it treats an empty allow-list as permissive                                                                                                                                                   0.13s  
  ✓ it matches domains case-insensitively                                                                                                                                                         0.13s  

   PASS  Tests\Feature\Identity\OnboardingTest
  ✓ it redirects authenticated users without onboarding from the dashboard to /onboarding                                                                                                         0.17s  
  ✓ it does not redirect already-onboarded users away from the dashboard                                                                                                                          0.40s  
  ✓ it renders the onboarding form with active SEAIT programs                                                                                                                                     0.47s  
  ✓ it persists program, year, and display name on submit                                                                                                                                         0.18s  
  ✓ it rejects invalid year levels                                                                                                                                                                0.18s  
  ✓ it redirects already-onboarded users away from the onboarding screen                                                                                                                          0.17s  

   PASS  Tests\Feature\Identity\SeaitSeedersTest
  ✓ it seeds a single SEAIT school row tied to Asia/Manila                                                                                                                                        0.20s  
  ✓ it seeds the six MVP colleges under SEAIT                                                                                                                                                     0.23s  
  ✓ it seeds the full SEAIT program list across all six colleges                                                                                                                                  0.25s  
  ✓ it is idempotent — running seeders twice does not duplicate rows                                                                                                                              0.19s  

   PASS  Tests\Feature\Identity\UserRoleTest
  ✓ it casts the role column to the UserRole enum                                                                                                                                                 0.15s  
  ✓ it defaults new users to the Student role                                                                                                                                                     0.14s  
  ✓ it exposes a flat list of role values for validation rules                                                                                                                                    0.13s  
  ✓ it UserRole enum has correct labels                                                                                                                                                           0.13s  

   PASS  Tests\Feature\Lends\LendTest
  ✓ it records a lend for a matched request with a physical resource                                                                                                                              0.28s  
  ✓ it refuses to record a lend for a non-matched request                                                                                                                                         0.27s  
  ✓ it refuses to record a lend for an offer without a resource                                                                                                                                   0.25s  
  ✓ it refuses to record a duplicate lend for the same offer                                                                                                                                      0.27s  
  ✓ it returns a borrowed resource and marks availability as available                                                                                                                            0.25s  
  ✓ it refuses to return resource if not the borrower                                                                                                                                             0.25s  
  ✓ it refuses to return an already returned resource                                                                                                                                             0.25s  
  ✓ it detects overdue lends                                                                                                                                                                      0.24s  
  ✓ it detects returned lends                                                                                                                                                                     0.26s  
  ✓ it detects lends due soon                                                                                                                                                                     0.24s  
  ✓ it dispatches return reminder notification for lends due within 2 days                                                                                                                        0.29s  
  ✓ it does not dispatch return reminder for already returned lends                                                                                                                               0.27s  
  ✓ it renders the lends index page                                                                                                                                                               1.03s  
  ✓ it records a lend via the record route                                                                                                                                                        0.26s  
  ✓ it returns a resource via the return route                                                                                                                                                    0.45s  
  ✓ it LendCondition enum has expected values                                                                                                                                                     0.30s  

   PASS  Tests\Feature\Moderation\EnumTest
  ✓ ReportStatus enum has expected cases                                                                                                                                                          0.19s  
  ✓ ReportReason enum has expected cases                                                                                                                                                          0.13s  
  ✓ ReportedType enum has expected cases                                                                                                                                                          0.13s  

   PASS  Tests\Feature\Moderation\ModerationTest
  ✓ it creates a report on a resource                                                                                                                                                             0.25s  
  ✓ it creates a report on a message                                                                                                                                                              0.36s  
  ✓ it creates a report on a user                                                                                                                                                                 0.37s  
  ✓ it prevents duplicate reports from the same user                                                                                                                                              0.28s  
  ✓ it refuses to report a non-existent entity                                                                                                                                                    0.29s  
  ✓ it resolves a report as actioned and deducts karma                                                                                                                                            0.33s  
  ✓ it resolves a report as dismissed without deducting karma                                                                                                                                     0.30s  
  ✓ it refuses to resolve an already-resolved report                                                                                                                                              0.27s  
  ✓ it suspends a user and creates an audit log entry                                                                                                                                             0.27s  
  ✓ it unsuspends a user and creates an audit log entry                                                                                                                                           0.30s  
  ✓ it refuses to suspend an admin                                                                                                                                                                0.30s  
  ✓ it refuses to suspend yourself                                                                                                                                                                0.29s  
  ✓ it creates an audit log entry                                                                                                                                                                 0.27s  
  ✓ it stores a report via the HTTP route                                                                                                                                                         0.32s  
  ✓ it renders the moderation dashboard for moderators                                                                                                                                            0.77s  
  ✓ it renders the admin dashboard for admins                                                                                                                                                     0.77s  
  ✓ it blocks students from the moderation dashboard                                                                                                                                              0.28s  
  ✓ it blocks students from the admin dashboard                                                                                                                                                   0.27s  
  ✓ it admin can assign a moderator to a program                                                                                                                                                  0.36s  
  ✓ it admin can remove a moderator from a program                                                                                                                                                0.30s  
  ✓ it admin can suspend a user                                                                                                                                                                   0.28s  
  ✓ it admin can unsuspend a user                                                                                                                                                                 0.33s  
  ✓ it moderator can resolve a report via HTTP                                                                                                                                                    0.32s  
  ✓ it moderator can suspend a user via HTTP                                                                                                                                                      0.30s  
  ✓ it suspended user is blocked from accessing routes                                                                                                                                            0.30s  
  ✓ it user with expired suspension can access routes                                                                                                                                             0.31s  
  ✓ it User model has correct role helper methods                                                                                                                                                 0.30s  
  ✓ it prevents self-report via CreateReport action                                                                                                                                               0.29s  
  ✓ it prevents reporting own message                                                                                                                                                             0.32s  
  ✓ it prevents reporting own resource                                                                                                                                                            0.48s  
  ✓ it snapshots message preview into audit log when report actioned on message                                                                                                                   0.45s  
  ✓ it ResolveReport actioned on resource archives it                                                                                                                                             0.40s  
  ✓ it SuspendUser accepts days and reason parameters                                                                                                                                             0.49s  
  ✓ it Report model returns correct status labels                                                                                                                                                 0.28s  
  ✓ it ReportSchoolScope filters reports by reporter school                                                                                                                                       0.29s  
  ✓ it non-moderator cannot resolve a report                                                                                                                                                      0.29s  
  ✓ it isSuspended returns false for past suspensions                                                                                                                                             0.27s  
  ✓ it isSuspended returns true for future suspensions                                                                                                                                            0.26s  
  ✓ it isSuspended returns false when suspended_until is null                                                                                                                                     0.28s  

   PASS  Tests\Feature\ProfileTest
  ✓ profile page is displayed for an onboarded user                                                                                                                                               0.83s  
  ✓ profile information can be updated                                                                                                                                                            0.29s  
  ✓ email verification status is unchanged when the email address is unchanged                                                                                                                    0.19s  
  ✓ user can delete their account                                                                                                                                                                 0.16s  
  ✓ correct password must be provided to delete account                                                                                                                                           0.17s  

   PASS  Tests\Feature\Reputation\KarmaTest
  ✓ it creates a karma event and recalculates user karma                                                                                                                                          0.20s  
  ✓ it awards +5 karma for uploading a resource                                                                                                                                                   0.20s  
  ✓ it awards +5 karma when a resource gets saved                                                                                                                                                 0.19s  
  ✓ it awards +10 karma for fulfilling a request                                                                                                                                                  0.24s  
  ✓ it awards +2 karma for a chat message marked helpful                                                                                                                                          0.29s  
  ✓ it accumulates karma across multiple events                                                                                                                                                   0.33s  
  ✓ it awards -5 karma for a confirmed report                                                                                                                                                     0.25s  
  ✓ it assigns the correct badge tier based on karma                                                                                                                                              0.26s  
  ✓ it renders the profile page with karma and badge                                                                                                                                              0.46s  
  ✓ it renders the leaderboard for the user's program                                                                                                                                             0.81s  
  ✓ it sorts leaderboard by karma descending                                                                                                                                                      0.25s  
  ✓ it KarmaEventReason returns correct points for each reason                                                                                                                                    0.17s  
  ✓ it KarmaEventReason values returns all cases                                                                                                                                                  0.25s  
  ✓ it BadgeTier labels are correct                                                                                                                                                               0.22s  
  ✓ it BadgeTier threshold returns correct karma requirements                                                                                                                                     0.21s  
  ✓ it BadgeTier fromKarma correctly resolves tiers                                                                                                                                               0.20s  

   PASS  Tests\Feature\Requests\AcceptOfferTest
  ✓ it accepts an offer and marks the request as fulfilled                                                                                                                                        0.36s  
  ✓ it rejects other pending offers when one is accepted                                                                                                                                          0.38s  
  ✓ it refuses if a non-requester tries to accept                                                                                                                                                 0.27s  
  ✓ it refuses to accept an already-accepted offer                                                                                                                                                0.28s  
  ✓ it accepts an offer via the accept route                                                                                                                                                      0.31s  

   PASS  Tests\Feature\Requests\CreateRequestTest
  ✓ it creates a request and stamps it with the requester                                                                                                                                         0.27s  
  ✓ it refuses to create a request for a subject in another school                                                                                                                                0.28s  
  ✓ it refuses to create a request when user already has 5 open requests                                                                                                                          0.29s  
  ✓ it requires an onboarded user with a school                                                                                                                                                   0.31s  
  ✓ it refuses to create a request if posted within 10 minutes of the last one                                                                                                                    0.32s  
  ✓ it stores a request via the POST route                                                                                                                                                        0.38s  
  ✓ it renders the request create page                                                                                                                                                            1.14s  
  ✓ it renders the request board index page                                                                                                                                                       1.12s  

   PASS  Tests\Feature\Requests\OfferTest
  ✓ it creates an offer on an open request                                                                                                                                                        0.34s  
  ✓ it refuses a second offer from the same user                                                                                                                                                  0.30s  
  ✓ it refuses an offer on a closed request                                                                                                                                                       0.29s  
  ✓ it accepts an offer with a matching resource                                                                                                                                                  0.28s  
  ✓ it posts an offer via the route                                                                                                                                                               0.29s  

   PASS  Tests\Feature\Requests\RouteRequestTest
  ✓ it routes a request to programs that have the subject in their curriculum                                                                                                                     0.41s  
  ✓ it applies the self-program penalty correctly                                                                                                                                                 0.27s  
  ✓ it does not notify the requester                                                                                                                                                              0.27s  
  ✓ it dispatches the NotifyRoutedUsers job                                                                                                                                                       0.28s  
  ✓ it falls back to routing only the requester's program when subject is not in any curriculum                                                                                                   0.28s  
  ✓ it enforces the 3-notifications-per-day cap per user                                                                                                                                          0.27s  
  ✓ it routing is idempotent — running twice does not double routes                                                                                                                               0.42s  
  ✓ it routing to programs with existing resources includes fulfillment data                                                                                                                      0.33s  
  ✓ it cross-post request jobs are dispatched for off-curriculum subjects                                                                                                                         0.31s  

   WARN  Tests\Feature\SmokeTest
  ✓ it returns a successful response from the home page                                                                                                                                           0.18s  
  ✓ it exposes the configured app name                                                                                                                                                            0.25s  
  ✓ it uses Asia/Manila as the default timezone for the SEAIT pilot                                                                                                                               0.17s  
  ✓ it renders the help page                                                                                                                                                                      0.36s  
  ✓ it renders the AUP page                                                                                                                                                                       0.63s  
  ✓ it redirects unauthenticated users from dashboard to login                                                                                                                                    0.15s  
  - it rate limits excessive POST requests → Throttle middleware returns 302 redirect in test environment — rate limiting verified at route level via throttle middleware declarations.           0.70s  
  ✓ it returns 200 from the up healthcheck endpoint                                                                                                                                               0.25s  
  ✓ it renders the login page                                                                                                                                                                     0.21s  
  ✓ it renders the registration page                                                                                                                                                              0.20s  
  ✓ it authenticated dashboard loads for onboarded user                                                                                                                                           0.19s  
  ✓ it leaderboard page loads                                                                                                                                                                     0.28s  
  ✓ it catalog browse loads for authenticated user                                                                                                                                                0.39s  
  ✓ it expire requests command runs without error                                                                                                                                                 0.27s  

  Tests:    1 skipped, 229 passed (568 assertions)
  Duration: 120.47s

PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> php artisan test --testsuite=Unit

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                                                                                                             0.01s  

  Tests:    1 passed (1 assertions)
  Duration: 0.25s

PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> php artisan view:clear
>>   php artisan view:cache

   INFO  Compiled views cleared successfully.  



   INFO  Blade templates cached successfully.  

PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> ./vendor/bin/pint --test

  .....................................................................................................................................................................................................
  .....................................

  ───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────── Laravel  
    PASS   .................................................................................................................................................................................. 234 files  

PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> ./vendor/bin/phpstan analyse
Note: Using configuration file C:\Users\ADMIN\OneDrive\Desktop\Another_Project\phpstan.neon.
 185/185 [============================] 100%


                                                                                                                        
 [OK] No errors                                                                                                         
                                                                                                                        

PS C:\Users\ADMIN\OneDrive\Desktop\Another_Project> ./vendor/bin/pest

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                                                                                                             0.01s  

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                                                                                                                                  2.25s  
  ✓ users can authenticate using the login screen                                                                                                                                                 0.27s  
  ✓ users can not authenticate with invalid password                                                                                                                                              0.39s  
  ✓ users can logout                                                                                                                                                                              0.18s  

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                                                                                                                                     0.38s  
  ✓ email can be verified                                                                                                                                                                         0.18s  
  ✓ email is not verified with invalid hash                                                                                                                                                       0.30s  

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                                                                                                                                                       1.10s  
  ✓ password can be confirmed                                                                                                                                                                     0.21s  
  ✓ password is not confirmed with invalid password                                                                                                                                               0.40s  

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                                                                                                                                    0.92s  
  ✓ reset password link can be requested                                                                                                                                                          0.34s  
  ✓ reset password screen can be rendered                                                                                                                                                         1.79s  
  ✓ password can be reset with valid token                                                                                                                                                        0.38s  

   PASS  Tests\Feature\Auth\PasswordUpdateTest
  ✓ password can be updated                                                                                                                                                                       0.19s  
  ✓ correct password must be provided to update password                                                                                                                                          0.21s  

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                                                                                                                                                           2.30s  
  ✓ new users can register with a SEAIT email and land on onboarding                                                                                                                              2.51s  
  ✓ registration rejects email domains outside the allow-list                                                                                                                                     0.19s  

   PASS  Tests\Feature\Catalog\CreateResourceTest
  ✓ it creates a resource and stamps it with owner / school / program from the user                                                                                                               0.40s  
  ✓ it stores an uploaded file on the public disk and queues the watermark job                                                                                                                    0.37s  
  ✓ it does not queue the watermark job when no file is attached                                                                                                                                  0.32s  
  ✓ it refuses to create a resource for a subject in another school                                                                                                                               0.41s  
  ✓ it flips is_watermarked when the watermark job is run synchronously                                                                                                                           0.38s  

   PASS  Tests\Feature\Catalog\ResourceDownloadTest
  ✓ it downloads the original file for non-PDF resources                                                                                                                                          0.50s  
  ✓ it serves the download route for authenticated users                                                                                                                                          0.42s  
  ✓ it returns 404 for resources without a file                                                                                                                                                   0.56s  
  ✓ it redirects unauthenticated users from download route                                                                                                                                        0.45s  
  ✓ it increments save_count when a resource is saved via the route                                                                                                                               0.45s  

   PASS  Tests\Feature\Catalog\ResourceFormLivewireTest
  ✓ it rejects an empty title                                                                                                                                                                     1.22s  
  ✓ it creates a resource via the Livewire form and queues the watermark job                                                                                                                      0.42s  
  ✓ it rejects a file with a disallowed mimetype                                                                                                                                                  0.40s  

   PASS  Tests\Feature\Catalog\ResourceRoutesTest
  ✓ it renders the resources index for an onboarded user                                                                                                                                          8.51s  
  ✓ it renders the resource create form                                                                                                                                                           0.97s  
  ✓ it renders a resource detail page when accessible                                                                                                                                             1.10s  
  ✓ it blocks cross-program access for program_only resources                                                                                                                                     0.32s  
  ✓ it returns 404 when the resource belongs to a different school                                                                                                                                0.28s  
  ✓ it redirects unauthenticated users away from resources                                                                                                                                        0.27s  

   PASS  Tests\Feature\Catalog\SeaitSubjectsSeederTest
  ✓ it seeds the GE core subjects shared across the pilot trio                                                                                                                                    0.44s  
  ✓ it seeds at least one subject for each pilot program                                                                                                                                          0.35s  
  ✓ it persists subject aliases for student shorthand                                                                                                                                             0.32s  
  ✓ it is idempotent — running the subjects seeder twice produces stable counts                                                                                                                   0.39s  
  ✓ it records typical year level and weight on program_subjects rows                                                                                                                             0.32s  

   PASS  Tests\Feature\Catalog\SearchResourcesTest
  ✓ it lists only resources from the viewer's school                                                                                                                                              0.67s  
  ✓ it hides program_only resources from outside the program                                                                                                                                      0.35s  
  ✓ it filters by subject, type, program, and year                                                                                                                                                0.37s  
  ✓ it searches across title and description (LIKE on sqlite)                                                                                                                                     0.35s  
  ✓ it excludes archived and soft-deleted resources from search                                                                                                                                   0.27s  

   PASS  Tests\Feature\Catalog\ShelfTest
  ✓ it creates a default shelf on first save and adds the resource                                                                                                                                0.28s  
  ✓ it toggles a resource off the shelf and decrements save_count                                                                                                                                 0.25s  
  ✓ it reports isSaved correctly                                                                                                                                                                  0.24s  
  ✓ it reuses the existing shelf on subsequent saves                                                                                                                                              0.27s  
  ✓ it renders the shelf page for an onboarded user                                                                                                                                               0.81s  
  ✓ it shows saved resources on the shelf page                                                                                                                                                    0.68s  
  ✓ it toggle-save route saves a resource and redirects back                                                                                                                                      0.37s  

   PASS  Tests\Feature\Chat\ChatAccessTest
  ✓ it lists only rooms scoped to the user's program and year                                                                                                                                     0.95s  
  ✓ it lets a student open their own program room                                                                                                                                                 1.46s  
  ✓ it forbids a student from opening another program's room                                                                                                                                      0.31s  
  ✓ it forbids a student from opening a wrong-year sub-channel                                                                                                                                    0.30s  
  ✓ it redirects unauthenticated users to login                                                                                                                                                   0.25s  
  ✓ it does not pollute college reference                                                                                                                                                         0.39s  
  ✓ it suspended user is forbidden from chat listing                                                                                                                                              0.32s  
  ✓ it suspended user is forbidden from opening a chat room                                                                                                                                       0.34s  
  ✓ it expired suspension allows chat access                                                                                                                                                      0.30s  
  ✓ it prevents multiple @mention of same user                                                                                                                                                    0.26s  
  ✓ it rejects attachment with disallowed MIME type                                                                                                                                               0.25s  

   PASS  Tests\Feature\Chat\PostChatMessageTest
  ✓ it persists the message and dispatches the broadcast event                                                                                                                                    0.35s  
  ✓ it refuses to post an empty message without an attachment                                                                                                                                     0.28s  
  ✓ it resolves @display_name mentions and sends a database notification                                                                                                                          0.26s  
  ✓ it does not mention the sender themselves                                                                                                                                                     0.28s  
  ✓ it broadcasts on the chat-room scoped private channel                                                                                                                                         0.27s  

   PASS  Tests\Feature\Chat\ProvisionChatRoomsTest
  ✓ it creates one program room plus year sub-rooms for every active program                                                                                                                      0.25s  
  ✓ it is idempotent — running twice does not duplicate rooms                                                                                                                                     0.29s  
  ✓ it uses program slugs that include the program code                                                                                                                                           0.24s  

   PASS  Tests\Feature\Chat\RoomConversationLivewireTest
  ✓ it renders the composer with the empty-state message                                                                                                                                          0.35s  
  ✓ it persists a message via the Livewire component                                                                                                                                              0.38s  
  ✓ it rejects empty submissions                                                                                                                                                                  0.30s  

   PASS  Tests\Feature\ExampleTest
  ✓ it returns a successful response                                                                                                                                                              0.70s  

   PASS  Tests\Feature\Feedback\FeedbackTest
  ✓ it renders the feedback form for authenticated users                                                                                                                                          0.84s  
  ✓ it stores feedback as a bug report                                                                                                                                                            0.35s  
  ✓ it stores feedback as a feature request                                                                                                                                                       0.32s  
  ✓ it defaults type to feedback when no type is provided                                                                                                                                         0.24s  
  ✓ it rejects feedback shorter than 5 characters                                                                                                                                                 0.25s  
  ✓ it rejects feedback longer than 2000 characters                                                                                                                                               0.31s  
  ✓ it flashes a thank-you message on success                                                                                                                                                     0.29s  
  ✓ it blocks unauthenticated users from the feedback form                                                                                                                                        0.24s  
  ✓ it blocks unauthenticated users from submitting feedback                                                                                                                                      0.25s  

   PASS  Tests\Feature\Identity\AllowedEmailDomainTest
  ✓ it accepts emails from configured domains                                                                                                                                                     0.16s  
  ✓ it rejects emails outside the allow-list                                                                                                                                                      0.14s  
  ✓ it treats an empty allow-list as permissive                                                                                                                                                   0.14s  
  ✓ it matches domains case-insensitively                                                                                                                                                         0.14s  

   PASS  Tests\Feature\Identity\OnboardingTest
  ✓ it redirects authenticated users without onboarding from the dashboard to /onboarding                                                                                                         0.18s  
  ✓ it does not redirect already-onboarded users away from the dashboard                                                                                                                          0.48s  
  ✓ it renders the onboarding form with active SEAIT programs                                                                                                                                     0.72s  
  ✓ it persists program, year, and display name on submit                                                                                                                                         0.25s  
  ✓ it rejects invalid year levels                                                                                                                                                                0.22s  
  ✓ it redirects already-onboarded users away from the onboarding screen                                                                                                                          0.18s  

   PASS  Tests\Feature\Identity\SeaitSeedersTest
  ✓ it seeds a single SEAIT school row tied to Asia/Manila                                                                                                                                        0.17s  
  ✓ it seeds the six MVP colleges under SEAIT                                                                                                                                                     0.16s  
  ✓ it seeds the full SEAIT program list across all six colleges                                                                                                                                  0.19s  
  ✓ it is idempotent — running seeders twice does not duplicate rows                                                                                                                              0.25s  

   PASS  Tests\Feature\Identity\UserRoleTest
  ✓ it casts the role column to the UserRole enum                                                                                                                                                 0.20s  
  ✓ it defaults new users to the Student role                                                                                                                                                     0.24s  
  ✓ it exposes a flat list of role values for validation rules                                                                                                                                    0.21s  
  ✓ it UserRole enum has correct labels                                                                                                                                                           0.16s  

   PASS  Tests\Feature\Lends\LendTest
  ✓ it records a lend for a matched request with a physical resource                                                                                                                              0.29s  
  ✓ it refuses to record a lend for a non-matched request                                                                                                                                         0.27s  
  ✓ it refuses to record a lend for an offer without a resource                                                                                                                                   0.25s  
  ✓ it refuses to record a duplicate lend for the same offer                                                                                                                                      0.29s  
  ✓ it returns a borrowed resource and marks availability as available                                                                                                                            0.28s  
  ✓ it refuses to return resource if not the borrower                                                                                                                                             0.28s  
  ✓ it refuses to return an already returned resource                                                                                                                                             0.24s  
  ✓ it detects overdue lends                                                                                                                                                                      0.26s  
  ✓ it detects returned lends                                                                                                                                                                     0.29s  
  ✓ it detects lends due soon                                                                                                                                                                     0.24s  
  ✓ it dispatches return reminder notification for lends due within 2 days                                                                                                                        0.40s  
  ✓ it does not dispatch return reminder for already returned lends                                                                                                                               0.25s  
  ✓ it renders the lends index page                                                                                                                                                               1.17s  
  ✓ it records a lend via the record route                                                                                                                                                        0.28s  
  ✓ it returns a resource via the return route                                                                                                                                                    0.33s  
  ✓ it LendCondition enum has expected values                                                                                                                                                     0.30s  

   PASS  Tests\Feature\Moderation\EnumTest
  ✓ ReportStatus enum has expected cases                                                                                                                                                          0.19s  
  ✓ ReportReason enum has expected cases                                                                                                                                                          0.15s  
  ✓ ReportedType enum has expected cases                                                                                                                                                          0.14s  

   PASS  Tests\Feature\Moderation\ModerationTest
  ✓ it creates a report on a resource                                                                                                                                                             0.25s  
  ✓ it creates a report on a message                                                                                                                                                              0.28s  
  ✓ it creates a report on a user                                                                                                                                                                 0.26s  
  ✓ it prevents duplicate reports from the same user                                                                                                                                              0.26s  
  ✓ it refuses to report a non-existent entity                                                                                                                                                    0.27s  
  ✓ it resolves a report as actioned and deducts karma                                                                                                                                            0.35s  
  ✓ it resolves a report as dismissed without deducting karma                                                                                                                                     0.25s  
  ✓ it refuses to resolve an already-resolved report                                                                                                                                              0.41s  
  ✓ it suspends a user and creates an audit log entry                                                                                                                                             0.27s  
  ✓ it unsuspends a user and creates an audit log entry                                                                                                                                           0.26s  
  ✓ it refuses to suspend an admin                                                                                                                                                                0.25s  
  ✓ it refuses to suspend yourself                                                                                                                                                                0.24s  
  ✓ it creates an audit log entry                                                                                                                                                                 0.24s  
  ✓ it stores a report via the HTTP route                                                                                                                                                         0.24s  
  ✓ it renders the moderation dashboard for moderators                                                                                                                                            0.67s  
  ✓ it renders the admin dashboard for admins                                                                                                                                                     0.89s  
  ✓ it blocks students from the moderation dashboard                                                                                                                                              0.25s  
  ✓ it blocks students from the admin dashboard                                                                                                                                                   0.25s  
  ✓ it admin can assign a moderator to a program                                                                                                                                                  0.25s  
  ✓ it admin can remove a moderator from a program                                                                                                                                                0.24s  
  ✓ it admin can suspend a user                                                                                                                                                                   0.25s  
  ✓ it admin can unsuspend a user                                                                                                                                                                 0.31s  
  ✓ it moderator can resolve a report via HTTP                                                                                                                                                    0.27s  
  ✓ it moderator can suspend a user via HTTP                                                                                                                                                      0.43s  
  ✓ it suspended user is blocked from accessing routes                                                                                                                                            0.49s  
  ✓ it user with expired suspension can access routes                                                                                                                                             0.59s  
  ✓ it User model has correct role helper methods                                                                                                                                                 0.35s  
  ✓ it prevents self-report via CreateReport action                                                                                                                                               0.25s  
  ✓ it prevents reporting own message                                                                                                                                                             0.40s  
  ✓ it prevents reporting own resource                                                                                                                                                            0.35s  
  ✓ it snapshots message preview into audit log when report actioned on message                                                                                                                   0.30s  
  ✓ it ResolveReport actioned on resource archives it                                                                                                                                             0.31s  
  ✓ it SuspendUser accepts days and reason parameters                                                                                                                                             0.24s  
  ✓ it Report model returns correct status labels                                                                                                                                                 0.27s  
  ✓ it ReportSchoolScope filters reports by reporter school                                                                                                                                       0.24s  
  ✓ it non-moderator cannot resolve a report                                                                                                                                                      0.26s  
  ✓ it isSuspended returns false for past suspensions                                                                                                                                             0.23s  
  ✓ it isSuspended returns true for future suspensions                                                                                                                                            0.24s  
  ✓ it isSuspended returns false when suspended_until is null                                                                                                                                     0.35s  

   PASS  Tests\Feature\ProfileTest
  ✓ profile page is displayed for an onboarded user                                                                                                                                               1.32s  
  ✓ profile information can be updated                                                                                                                                                            0.16s  
  ✓ email verification status is unchanged when the email address is unchanged                                                                                                                    0.15s  
  ✓ user can delete their account                                                                                                                                                                 0.14s  
  ✓ correct password must be provided to delete account                                                                                                                                           0.15s  

   PASS  Tests\Feature\Reputation\KarmaTest
  ✓ it creates a karma event and recalculates user karma                                                                                                                                          0.29s  
  ✓ it awards +5 karma for uploading a resource                                                                                                                                                   0.25s  
  ✓ it awards +5 karma when a resource gets saved                                                                                                                                                 0.19s  
  ✓ it awards +10 karma for fulfilling a request                                                                                                                                                  0.17s  
  ✓ it awards +2 karma for a chat message marked helpful                                                                                                                                          0.17s  
  ✓ it accumulates karma across multiple events                                                                                                                                                   0.23s  
  ✓ it awards -5 karma for a confirmed report                                                                                                                                                     0.18s  
  ✓ it assigns the correct badge tier based on karma                                                                                                                                              0.17s  
  ✓ it renders the profile page with karma and badge                                                                                                                                              0.19s  
  ✓ it renders the leaderboard for the user's program                                                                                                                                             0.69s  
  ✓ it sorts leaderboard by karma descending                                                                                                                                                      0.21s  
  ✓ it KarmaEventReason returns correct points for each reason                                                                                                                                    0.19s  
  ✓ it KarmaEventReason values returns all cases                                                                                                                                                  0.16s  
  ✓ it BadgeTier labels are correct                                                                                                                                                               0.16s  
  ✓ it BadgeTier threshold returns correct karma requirements                                                                                                                                     0.28s  
  ✓ it BadgeTier fromKarma correctly resolves tiers                                                                                                                                               0.20s  

   PASS  Tests\Feature\Requests\AcceptOfferTest
  ✓ it accepts an offer and marks the request as fulfilled                                                                                                                                        0.39s  
  ✓ it rejects other pending offers when one is accepted                                                                                                                                          0.29s  
  ✓ it refuses if a non-requester tries to accept                                                                                                                                                 0.27s  
  ✓ it refuses to accept an already-accepted offer                                                                                                                                                0.26s  
  ✓ it accepts an offer via the accept route                                                                                                                                                      0.28s  

   PASS  Tests\Feature\Requests\CreateRequestTest
  ✓ it creates a request and stamps it with the requester                                                                                                                                         0.27s  
  ✓ it refuses to create a request for a subject in another school                                                                                                                                0.26s  
  ✓ it refuses to create a request when user already has 5 open requests                                                                                                                          0.23s  
  ✓ it requires an onboarded user with a school                                                                                                                                                   0.25s  
  ✓ it refuses to create a request if posted within 10 minutes of the last one                                                                                                                    0.24s  
  ✓ it stores a request via the POST route                                                                                                                                                        0.26s  
  ✓ it renders the request create page                                                                                                                                                            0.77s  
  ✓ it renders the request board index page                                                                                                                                                       1.07s  

   PASS  Tests\Feature\Requests\OfferTest
  ✓ it creates an offer on an open request                                                                                                                                                        0.47s  
  ✓ it refuses an offer from the requester themselves                                                                                                                                             0.28s  
  ✓ it refuses a second offer from the same user                                                                                                                                                  0.32s  
  ✓ it refuses an offer on a closed request                                                                                                                                                       0.29s  
  ✓ it accepts an offer with a matching resource                                                                                                                                                  0.26s  
  ✓ it posts an offer via the route                                                                                                                                                               0.25s  

   PASS  Tests\Feature\Requests\RouteRequestTest
  ✓ it routes a request to programs that have the subject in their curriculum                                                                                                                     0.30s  
  ✓ it applies the self-program penalty correctly                                                                                                                                                 0.26s  
  ✓ it picks users to notify from routed programs                                                                                                                                                 0.28s  
  ✓ it does not notify the requester                                                                                                                                                              0.25s  
  ✓ it dispatches the NotifyRoutedUsers job                                                                                                                                                       0.26s  
  ✓ it falls back to routing only the requester's program when subject is not in any curriculum                                                                                                   0.26s  
  ✓ it enforces the 3-notifications-per-day cap per user                                                                                                                                          0.25s  
  ✓ it routing is idempotent — running twice does not double routes                                                                                                                               0.26s  
  ✓ it routing to programs with existing resources includes fulfillment data                                                                                                                      0.25s  
  ✓ it cross-post request jobs are dispatched for off-curriculum subjects                                                                                                                         0.26s  
  ✓ it urgent requests are routed to all candidate programs                                                                                                                                       0.24s  

   WARN  Tests\Feature\SmokeTest
  ✓ it returns a successful response from the home page                                                                                                                                           0.17s  
  ✓ it exposes the configured app name                                                                                                                                                            0.14s  
  ✓ it uses Asia/Manila as the default timezone for the SEAIT pilot                                                                                                                               0.13s  
  ✓ it renders the help page                                                                                                                                                                      0.41s  
  ✓ it renders the AUP page                                                                                                                                                                       0.30s  
  ✓ it serves the landing page with StudHub branding                                                                                                                                              0.20s  
  ✓ it redirects unauthenticated users from dashboard to login                                                                                                                                    0.19s  
  - it rate limits excessive POST requests → Throttle middleware returns 302 redirect in test environment — rate limiting verified at route level via throttle middleware declarations.           0.16s  
  ✓ it returns 200 from the up healthcheck endpoint                                                                                                                                               0.22s  
  ✓ it renders the login page                                                                                                                                                                     0.19s  
  ✓ it renders the registration page                                                                                                                                                              0.19s  
  ✓ it authenticated dashboard loads for onboarded user                                                                                                                                           0.18s  
  ✓ it leaderboard page loads                                                                                                                                                                     0.26s  
  ✓ it catalog browse loads for authenticated user                                                                                                                                                0.27s  
  ✓ it request board loads for authenticated user                                                                                                                                                 0.28s  
  ✓ it expire requests command runs without error                                                                                                                                                 0.25s  

  Tests:    1 skipped, 230 passed (569 assertions)
  Duration: 93.43s

Test run results: 
PS C:\Users\ADMIN> Get-Process -Name "node","php" -ErrorAction SilentlyContinue | Stop-Process -Force; if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }; $env:Path = "C:\xampp\php;$env:Path"; php artisan view:clear; php artisan config:clear; npm run build; php artisan serve
Could not open input file: artisan                                
Could not open input file: artisan
npm error code ENOENT
npm error syscall open
npm error path C:\Users\ADMIN\package.json
npm error errno -4058
npm error enoent Could not read package.json: Error: ENOENT: no such file or directory, open 'C:\Users\ADMIN\package.json'
npm error enoent This is related to npm not being able to find a file.
npm error enoent
npm error A complete log of this run can be found in: C:\Users\ADMIN\AppData\Local\npm-cache\_logs\2026-05-29T10_33_39_305Z-debug-0.log
Could not open input file: artisan
PS C:\Users\ADMIN> Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force                                                                                                          
>> Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force         
>> if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }
PS C:\Users\ADMIN> Get-Process -Name "node","php" -ErrorAction SilentlyContinue | Stop-Process -Force; if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }; $env:Path = "C:\xampp\php;$env:Path"; php artisan view:clear; php artisan config:clear; npm run build; php artisan serve
Could not open input file: artisan                                
Could not open input file: artisan
npm error code ENOENT
npm error syscall open
npm error path C:\Users\ADMIN\package.json
npm error errno -4058
npm error enoent Could not read package.json: Error: ENOENT: no such file or directory, open 'C:\Users\ADMIN\package.json'
npm error enoent This is related to npm not being able to find a file.
npm error enoent
npm error A complete log of this run can be found in: C:\Users\ADMIN\AppData\Local\npm-cache\_logs\2026-05-29T10_34_05_016Z-debug-0.log
Could not open input file: artisan
PS C:\Users\ADMIN> 