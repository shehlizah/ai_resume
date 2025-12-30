# API & Route Catalog (Web)

Source: routes/web.php and routes/auth.php. All routes are web (no routes/api.php). Includes middleware/role/env flags for mobile integration. Synced to prod route dump (php artisan route:list --json).

Legend (Middleware/Env column):
- `auth` = logged-in required; `guest` = only guests; `role:user|admin|employer` as noted
- `signed`, `throttle:x,y` as defined; `CheckActivePackage` = requires active package; `local`, `staging` env flags noted
- `Volt` = Livewire Volt page (served via GET)

## Public / Guest
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | / | Homepage | home | — |
| GET | /pricing | Pricing page (user-facing) | packages | — |
| GET | /lang/{locale} | Switch UI locale | language.switch | — |
| POST | /webhooks/stripe | Stripe webhook receiver | stripe.webhook | — |
| GET | /payment/stripe/success | Stripe success return | user.payment.stripe.success | — (session-based) |
| POST | /payment/stripe/checkout | Stripe checkout | user.payment.stripe.checkout | — (session-based) |
| POST | /payment/paypal/checkout | PayPal checkout | user.payment.paypal.checkout | — (session-based) |
| POST | /payment/paypal/success | PayPal success | user.payment.paypal.success | — (session-based) |
| GET | /payment/paypal/cancel | PayPal cancel | user.payment.paypal.cancel | — |
| GET | /up | Health check (framework) | — | — |
| GET | /storage/{path} | Public storage files | storage.local | — |
| GET | /livewire/livewire.js | Livewire asset | — | — |
| GET | /livewire/livewire.min.js.map | Livewire source map | — | — |
| GET | /livewire/preview-file/{filename} | Livewire upload preview | livewire.preview-file | web |
| POST | /livewire/update | Livewire update | livewire.update | web |
| POST | /livewire/upload-file | Livewire upload | livewire.upload-file | web, throttle:60,1 |

## Auth (Guest Views)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| Volt | /login | Login page | login | guest |
| Volt | /register | User registration | register | guest |
| Volt | /register/employer | Employer registration | register.employer | guest |
| Volt | /forgot-password | Request password reset | password.request | guest |
| Volt | /reset-password/{token} | Reset password form | password.reset | guest |

## Auth (Logged-In Utilities)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| Volt | /verify-email | Email verification prompt | verification.notice | auth |
| GET | /verify-email/{id}/{hash} | Verify email link | verification.verify | auth, signed, throttle:6,1 |
| Volt | /confirm-password | Confirm password | password.confirm | auth |
| POST | /logout | Logout | logout | auth |

## User Dashboard (auth + role:user,admin)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /dashboard | Dashboard home | user.dashboard | auth, role:user|admin |
| GET | /dashboard/stats | Dashboard stats data | user.dashboard.stats | auth, role:user|admin |
| Redirect | /settings → /settings/profile | Settings redirect | — | auth, role:user|admin |
| Volt | /settings/profile | Profile settings | settings.profile | auth, role:user|admin |
| Volt | /settings/password | Password settings | settings.password | auth, role:user|admin |
| Volt | /settings/monetization | Monetization settings | settings.monetization | auth, role:user|admin |
| ANY | /settings | Settings redirect | — | auth, role:user|admin |
| GET | /user/pricing | Pricing (auth) | user.pricing | auth, role:user|admin |

### User Subscriptions (prefix /subscription)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /subscription/dashboard | Subscription dashboard | user.subscription.dashboard | auth, role:user|admin |
| GET | /subscription/checkout/{plan} | Checkout a plan | user.subscription.checkout | auth, role:user|admin |
| POST | /subscription/cancel | Cancel plan | user.subscription.cancel | auth, role:user|admin |
| POST | /subscription/resume | Resume plan | user.subscription.resume | auth, role:user|admin |
| POST | /subscription/change-billing | Change billing period | user.subscription.change-billing | auth, role:user|admin |

### Resumes (prefix /resumes)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /resumes/ | List resumes | user.resumes.index | auth, role:user|admin |
| GET | /resumes/view/{id} | View resume | user.resumes.view | auth, role:user|admin |
| DELETE | /resumes/{id} | Delete resume | user.resumes.destroy | auth, role:user|admin |
| GET | /resumes/choose | Choose template | user.resumes.choose | auth, role:user|admin |
| GET | /resumes/create | Create (alias choose) | user.resumes.create | auth, role:user|admin |
| GET | /resumes/preview/{template_id} | Preview template | user.resumes.preview | auth, role:user|admin |
| GET | /resumes/print-preview/{id} | Print preview | user.resumes.print-preview | auth, role:user|admin |
| GET | /resumes/fill/{template_id} | Fill form | user.resumes.fill | auth, role:user|admin |
| POST | /resumes/generate | Generate resume | user.resumes.generate | auth, role:user|admin |
| GET | /resumes/success/{id} | Success page | user.resumes.success | auth, role:user|admin |
| POST | /resumes/generate-experience-ai | AI experience | user.resumes.generate-experience-ai | auth, role:user|admin |
| POST | /resumes/generate-skills-ai | AI skills | user.resumes.generate-skills-ai | auth, role:user|admin |
| POST | /resumes/generate-education-ai | AI education | user.resumes.generate-education-ai | auth, role:user|admin |
| POST | /resumes/generate-summary-ai | AI summary | user.resumes.generate-summary-ai | auth, role:user|admin |
| POST | /resumes/upload-temp | Temp upload (for jobs/interview) | user.resumes.upload-temp | auth, role:user|admin |
| GET | /resumes/download/{id} | Download (package required) | user.resumes.download | auth, role:user|admin, CheckActivePackage |
| GET | /user/resumes | Legacy list alias | user.resumes | auth, role:user|admin |

### Cover Letters (prefix /cover-letters)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /cover-letters/ | List | user.cover-letters.index | auth, role:user|admin |
| GET | /cover-letters/{coverLetter}/view | View | user.cover-letters.view | auth, role:user|admin |
| DELETE | /cover-letters/{coverLetter}/destroy | Delete | user.cover-letters.destroy | auth, role:user|admin |
| GET | /cover-letters/create | Create form | user.cover-letters.create | auth, role:user|admin |
| POST | /cover-letters/store | Store | user.cover-letters.store | auth, role:user|admin |
| GET | /cover-letters/templates | Template list | user.cover-letters.select-template | auth, role:user|admin |
| GET | /cover-letters/templates/{template}/use | Use template | user.cover-letters.create-from-template | auth, role:user|admin |
| GET | /cover-letters/{coverLetter}/edit | Edit | user.cover-letters.edit | auth, role:user|admin |
| PUT | /cover-letters/{coverLetter}/update | Update | user.cover-letters.update | auth, role:user|admin |
| POST | /cover-letters/generate-ai | AI generate | user.cover-letters.generate-ai | auth, role:user|admin |
| GET | /cover-letters/{coverLetter}/download | Download (package required) | user.cover-letters.download | auth, role:user|admin, CheckActivePackage |
| GET | /cover-letters/{coverLetter}/print | Print (package required) | user.cover-letters.print | auth, role:user|admin, CheckActivePackage |

### Job Finder (prefix /jobs)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /jobs/recommended | Recommended jobs (view) | user.jobs.recommended | auth, role:user|admin |
| POST | /jobs/recommended | Generate recommended | user.jobs.recommended | auth, role:user|admin |
| GET | /jobs/by-location | Jobs by location (view) | user.jobs.by-location | auth, role:user|admin |
| POST | /jobs/by-location | Generate by location | user.jobs.by-location | auth, role:user|admin |
| POST | /jobs/reset-session | Reset session limit | user.jobs.reset-session | auth, role:user|admin |
| POST | /jobs/{jobId}/apply | Apply to job | user.jobs.apply | auth, role:user|admin |

### Interview Prep (prefix /interview)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /interview/prep | Prep page | user.interview.prep | auth, role:user|admin |
| POST | /interview/prep/generate | Generate prep | user.interview.generate-prep | auth, role:user|admin |
| GET | /interview/questions | Practice questions | user.interview.questions | auth, role:user|admin |
| GET | /interview/ai-practice | AI mock interview (pro) | user.interview.ai-practice | auth, role:user|admin, CheckActivePackage |
| POST | /interview/ai-practice/start | Start AI session (pro) | user.interview.ai-practice-start | auth, role:user|admin, CheckActivePackage |
| POST | /interview/ai-practice/answer | Submit AI answer (pro) | user.interview.ai-practice-answer | auth, role:user|admin, CheckActivePackage |
| GET | /interview/ai-results/{sessionId} | View AI results (pro) | user.interview.ai-results | auth, role:user|admin, CheckActivePackage |
| GET | /interview/expert | Expert booking (pro) | user.interview.expert | auth, role:user|admin, CheckActivePackage |
| GET | /interview/my-sessions | My expert sessions (pro) | user.interview.my-sessions | auth, role:user|admin, CheckActivePackage |

### Add-Ons (prefix /add-ons)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /add-ons/ | Browse add-ons | user.add-ons.index | auth, role:user|admin |
| GET | /add-ons/{addOn} | View add-on | user.add-ons.show | auth, role:user|admin |
| GET | /add-ons/my-add-ons | My add-ons | user.add-ons.my-add-ons | auth, role:user|admin |
| GET | /add-ons/{addOn}/checkout | Checkout | user.add-ons.checkout | auth, role:user|admin |
| POST | /add-ons/{addOn}/purchase | Purchase | user.add-ons.purchase | auth, role:user|admin |
| GET | /add-ons/payment/{userAddOn}/stripe | Stripe checkout | user.add-ons.stripe-checkout | auth, role:user|admin |
| GET | /add-ons/payment/{userAddOn}/success | Stripe success | user.add-ons.payment-success | auth, role:user|admin |
| GET | /add-ons/{addOn}/access | Access purchased content | user.add-ons.access | auth, role:user|admin |
| GET | /add-ons/{addOn}/job-search | Job search (AI) | user.add-ons.job-search | auth, role:user|admin |
| POST | /add-ons/{addOn}/generate-jobs | Generate job recs (AI) | user.add-ons.generate-jobs | auth, role:user|admin |
| GET | /add-ons/{addOn}/interview-prep | Interview prep (AI) | user.add-ons.interview-prep | auth, role:user|admin |
| POST | /add-ons/{addOn}/generate-interview | Generate interview prep (AI) | user.add-ons.generate-interview | auth, role:user|admin |

## Job Applications (any auth user)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /jobs/{job}/apply | Application form | jobs.apply.show | auth |
| POST | /jobs/{job}/apply | Submit application | jobs.apply.store | auth |

## Company Dashboard (auth + role:employer, prefix /company)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /company/dashboard | Company dashboard | company.dashboard | auth, role:employer |
| GET | /company/jobs/create | Create job form | company.jobs.create | auth, role:employer |
| POST | /company/jobs | Store job | company.jobs.store | auth, role:employer |
| GET | /company/jobs | List jobs | company.jobs.index | auth, role:employer |
| GET | /company/jobs/{job} | Show job | company.jobs.show | auth, role:employer |
| GET | /company/jobs/{job}/edit | Edit job | company.jobs.edit | auth, role:employer |
| PUT | /company/jobs/{job} | Update job | company.jobs.update | auth, role:employer |
| GET | /company/applications | All applications | company.applications.index | auth, role:employer |
| GET | /company/jobs/{job}/applications | Applications for job | company.jobs.applications | auth, role:employer |
| GET | /company/ai-matching | AI matching overview | company.ai-matching | auth, role:employer |
| GET | /company/ai-matching/candidates | Candidate matches list | company.ai-matching.candidates | auth, role:employer |
| GET | /company/ai-matching/{job} | AI matching for job | company.ai-matching.job | auth, role:employer |
| GET | /company/ai-matching/{job}/candidate/{match}/resume | Download match resume | company.ai-matching.candidate.resume | auth, role:employer |
| POST | /company/ai-matching/{job}/trigger-match | Trigger matching manually | company.ai-matching.trigger | auth, role:employer |
| GET | /company/packages | Company packages | company.packages | auth, role:employer |
| GET | /company/addons | Company add-ons | company.addons | auth, role:employer |
| GET | /company/packages/{slug}/checkout | Package checkout | company.packages.checkout | auth, role:employer |
| GET | /company/addons/{slug}/checkout | Add-on checkout | company.addons.checkout | auth, role:employer |
| POST | /company/payment/manual | Manual payment submit | company.payment.manual | auth, role:employer |
| POST | /company/payment/stripe | Stripe checkout | company.payment.stripe | auth, role:employer |
| GET | /company/payment/stripe/success | Stripe success | company.payment.stripe.success | auth, role:employer |
| GET | /company/payment/stripe/cancel | Stripe cancel | company.payment.stripe.cancel | auth, role:employer |
| GET | /company/debug-addons | Debug employer add-ons | company.debug-addons | auth, role:employer |
| GET | /company/trigger-old-job-matching | Trigger matching for old jobs | company.trigger-old-job-matching | auth, role:employer |
| GET | /company/cleanup-empty-resume-matches | Cleanup empty resume matches | company.cleanup-empty-matches | auth, role:employer |

## Admin Dashboard (auth + role:admin, prefix /admin)
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /admin/dashboard | Admin home | admin.dashboard | auth, role:admin |
| Resource | /admin/templates | Template CRUD (except show) | admin.templates.* | auth, role:admin |
| GET | /admin/templates/{id}/preview | Preview template | admin.templates.preview | auth, role:admin |
| POST | /admin/templates/{id}/preview-live | Live preview | admin.templates.preview-live | auth, role:admin |
| POST | /admin/templates/{id}/toggle-active | Toggle active | admin.templates.toggle-active | auth, role:admin |
| POST | /admin/templates/{id}/duplicate | Duplicate template | admin.templates.duplicate | auth, role:admin |
| GET | /admin/users | List users | admin.users.index | auth, role:admin |
| GET | /admin/users/create | Create user form | admin.users.create | auth, role:admin |
| POST | /admin/users | Store user | admin.users.store | auth, role:admin |
| GET | /admin/users/{id} | Show user | admin.users.show | auth, role:admin |
| GET | /admin/users/{id}/edit | Edit user | admin.users.edit | auth, role:admin |
| PUT | /admin/users/{id} | Update user | admin.users.update | auth, role:admin |
| DELETE | /admin/users/{id} | Delete user | admin.users.destroy | auth, role:admin |
| POST | /admin/users/{id}/toggle-status | Toggle status | admin.users.toggle-status | auth, role:admin |
| POST | /admin/users/{id}/toggle-lifetime-access | Toggle lifetime access | admin.users.toggle-lifetime-access | auth, role:admin |
| DELETE | /admin/users/{userId}/resumes/{resumeId} | Delete user resume | admin.users.delete-resume | auth, role:admin |
| GET | /admin/users/{userId}/resumes/{resumeId}/download | Download user resume | admin.users.download-resume | auth, role:admin |
| POST | /admin/users/bulk-action | Bulk action | admin.users.bulk-action | auth, role:admin |
| Resource | /admin/subscription-plans | Plan CRUD | admin.subscription-plans.* | auth, role:admin |
| POST | /admin/subscription-plans/{subscriptionPlan}/toggle-status | Toggle plan status | admin.subscription-plans.toggle-status | auth, role:admin |
| GET | /admin/subscriptions | List subscriptions | admin.subscriptions.index | auth, role:admin |
| GET | /admin/subscriptions/{subscription} | Show subscription | admin.subscriptions.show | auth, role:admin |
| POST | /admin/subscriptions/{subscription}/cancel | Cancel | admin.subscriptions.cancel | auth, role:admin |
| POST | /admin/subscriptions/{subscription}/activate | Activate | admin.subscriptions.activate | auth, role:admin |
| GET | /admin/payments | List payments | admin.payments.index | auth, role:admin |
| GET | /admin/payments/{payment} | Show payment | admin.payments.show | auth, role:admin |
| POST | /admin/payments/{payment}/approve | Approve payment | admin.payments.approve | auth, role:admin |
| POST | /admin/payments/{payment}/reject | Reject payment | admin.payments.reject | auth, role:admin |
| GET | /admin/payments/export/csv | Export payments CSV | admin.payments.export | auth, role:admin |
| GET | /admin/company-payments | List company payments | admin.company-payments.index | auth, role:admin |
| GET | /admin/company-payments/{payment} | Show company payment | admin.company-payments.show | auth, role:admin |
| POST | /admin/company-payments/{payment}/approve | Approve company payment | admin.company-payments.approve | auth, role:admin |
| POST | /admin/company-payments/{payment}/reject | Reject company payment | admin.company-payments.reject | auth, role:admin |
| GET | /admin/company-payments/{payment}/download-proof | Download proof | admin.company-payments.download-proof | auth, role:admin |
| GET | /admin/company-payments/{payment}/view-proof | View proof | admin.company-payments.view-proof | auth, role:admin |
| GET | /admin/company-payments/{payment}/debug-proof | Debug proof | admin.company-payments.debug-proof | auth, role:admin |
| GET | /admin/cover-letters | Cover letter dashboard | admin.cover-letters.index | auth, role:admin |
| GET | /admin/cover-letters/statistics | Cover letter statistics | admin.cover-letters.statistics | auth, role:admin |
| GET | /admin/cover-letters/templates | Templates list | admin.cover-letters.templates | auth, role:admin |
| GET | /admin/cover-letters/templates/create | Create template | admin.cover-letters.templates.create | auth, role:admin |
| POST | /admin/cover-letters/templates | Store template | admin.cover-letters.templates.store | auth, role:admin |
| GET | /admin/cover-letters/templates/{template} | Show template | admin.cover-letters.templates.show | auth, role:admin |
| GET | /admin/cover-letters/templates/{template}/edit | Edit template | admin.cover-letters.templates.edit | auth, role:admin |
| PUT | /admin/cover-letters/templates/{template} | Update template | admin.cover-letters.templates.update | auth, role:admin |
| DELETE | /admin/cover-letters/templates/{template} | Delete template | admin.cover-letters.templates.delete | auth, role:admin |
| POST | /admin/cover-letters/templates/{template}/toggle | Toggle template | admin.cover-letters.templates.toggle | auth, role:admin |
| POST | /admin/cover-letters/templates/{template}/duplicate | Duplicate template | admin.cover-letters.templates.duplicate | auth, role:admin |
| POST | /admin/cover-letters/templates/bulk-action | Templates bulk action | admin.cover-letters.templates.bulk-action | auth, role:admin |
| GET | /admin/cover-letters/user-cover-letters | User cover letters | admin.cover-letters.user-cover-letters | auth, role:admin |
| GET | /admin/cover-letters/user-cover-letters/{coverLetter} | View user cover letter | admin.cover-letters.view-cover-letter | auth, role:admin |
| DELETE | /admin/cover-letters/user-cover-letters/{coverLetter} | Delete user cover letter | admin.cover-letters.delete-cover-letter | auth, role:admin |
| POST | /admin/cover-letters/user-cover-letters/{coverLetter}/restore | Restore user cover letter | admin.cover-letters.restore | auth, role:admin |
| DELETE | /admin/cover-letters/user-cover-letters/{coverLetter}/permanent | Permanent delete | admin.cover-letters.permanent-delete | auth, role:admin |
| GET | /admin/cover-letters/export/cover-letters | Export cover letters | admin.cover-letters.export.cover-letters | auth, role:admin |
| GET | /admin/cover-letters/export/templates | Export templates | admin.cover-letters.export.templates | auth, role:admin |
| Resource | /admin/add-ons | Add-on CRUD | admin.add-ons.* | auth, role:admin |
| POST | /admin/add-ons/{addOn}/toggle-status | Toggle add-on | admin.add-ons.toggle-status | auth, role:admin |
| GET | /admin/add-ons/{addOn}/purchases | View add-on purchases | admin.add-ons.purchases | auth, role:admin |
| GET | /admin/jobs/user-activity | Job finder user activity | admin.jobs.user-activity | auth, role:admin |
| GET | /admin/jobs/api-settings | Job API settings | admin.jobs.api-settings | auth, role:admin |
| POST | /admin/jobs/api-settings | Update job API settings | admin.jobs.update-api-settings | auth, role:admin |
| GET | /admin/jobs/statistics | Job statistics | admin.jobs.statistics | auth, role:admin |
| GET | /admin/interviews/sessions | Interview sessions | admin.interviews.sessions | auth, role:admin |
| DELETE | /admin/interviews/sessions/{sessionId} | Delete session | admin.interviews.delete-session | auth, role:admin |
| GET | /admin/interviews/questions | Interview questions | admin.interviews.questions | auth, role:admin |
| GET | /admin/interviews/settings | Interview settings | admin.interviews.settings | auth, role:admin |
| POST | /admin/interviews/settings | Update interview settings | admin.interviews.update-settings | auth, role:admin |
| GET | /admin/templates/{id}/debug | Template debug | admin.templates.debug | auth, role:admin |

## Local / Debug
| Method | Path | Description | Name | Middleware / Env |
| --- | --- | --- | --- | --- |
| GET | /test-starter-templates | Test template count | — | web |
| GET | /debug-template/{id} | Template debug JSON | — | auth |
| GET | /debug/locale | Debug locale view | debug.locale | web |

Notes:
- Middleware/Env column shows auth/roles, env flags (when known), and package requirement markers needed for mobile integration.
- Volt denotes Livewire Volt page routes (served via GET under the hood).
