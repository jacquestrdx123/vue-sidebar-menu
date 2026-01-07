<?php

namespace App\Services\System;

use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuWebService
{
    /**
     * Cache user permissions to avoid repeated queries
     */
    private ?array $cachedUserPermissions = null;

    /**
     * Track if debug has been logged to avoid repeated logging
     */
    private bool $debugLogged = false;

    private function getMenuArray(): array
    {
        return [
            // Top level items (not in groups)

            // 'user-views' => [
            //     'label' => 'User Views',
            //     'url' => route('vue.user-views.index'),
            //     'icon' => 'heroicon-o-eye',
            //     'route' => 'vue.user-views.index',
            // ],

            // Grouped items
            'dashboards' => [
                'label' => 'Dashboards',
                'url' => '#',
                'icon' => 'heroicon-o-squares-2x2',
                'route' => null,
                'children' => [
                    'dashboard' => [
                        'label' => 'Dashboard',
                        'url' => route('vue.dashboard'),
                        'icon' => 'heroicon-o-home',
                        'route' => 'vue.dashboard',
                    ],
                    'support-tickets-dashboard' => [
                        'label' => 'Support Tickets Dashboard',
                        'url' => route('vue.dashboards.support-tickets'),
                        'icon' => 'heroicon-o-ticket',
                        'route' => 'vue.dashboards.support-tickets',
                    ],
                    'ppc-dashboard' => [
                        'label' => 'PPC Dashboard',
                        'url' => route('vue.dashboards.ppc'),
                        'icon' => 'heroicon-o-clipboard-document-list',
                        'route' => 'vue.dashboards.ppc',
                    ],
                    'proforma-invoices-dashboard' => [
                        'label' => 'Proforma Invoices Dashboard',
                        'url' => route('vue.dashboards.proforma-invoices'),
                        'icon' => 'heroicon-o-document-chart-bar',
                        'route' => 'vue.dashboards.proforma-invoices',
                    ],
                ],
            ],

            'member-settings' => [
                'label' => 'Member Settings',
                'url' => '#',
                'icon' => 'heroicon-o-cog-6-tooth',
                'route' => null,
                'children' => [
                    'application-decline-reasons' => [
                        'label' => 'Application Decline Reasons',
                        'url' => route('vue.application-decline-reasons.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.application-decline-reasons.index',
                    ],
                    'current-occupations' => [
                        'label' => 'Current Occupations',
                        'url' => route('vue.current-occupations.index'),
                        'icon' => 'heroicon-o-building-office',
                        'route' => 'vue.current-occupations.index',
                    ],
                    'employment-industries' => [
                        'label' => 'Employment Industries',
                        'url' => route('vue.employment-industries.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.employment-industries.index',
                    ],
                    'employment-sectors' => [
                        'label' => 'Employment Sectors',
                        'url' => route('vue.employment-sectors.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.employment-sectors.index',
                    ],
                    'employment-statuses' => [
                        'label' => 'Employment Statuses',
                        'url' => route('vue.employment-statuses.index'),
                        'icon' => 'heroicon-o-user-circle',
                        'route' => 'vue.employment-statuses.index',
                    ],
                    'home-languages' => [
                        'label' => 'Home Languages',
                        'url' => route('vue.home-languages.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.home-languages.index',
                    ],
                    'job-titles' => [
                        'label' => 'Job Titles',
                        'url' => route('vue.job-titles.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.job-titles.index',
                    ],
                    'marital-statuses' => [
                        'label' => 'Marital Statuses',
                        'url' => route('vue.marital-statuses.index'),
                        'icon' => 'heroicon-o-heart',
                        'route' => 'vue.marital-statuses.index',
                    ],
                    'member-classification-types' => [
                        'label' => 'Member Classification Types',
                        'url' => route('vue.member-classification-types.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.member-classification-types.index',
                    ],
                    'member-document-types' => [
                        'label' => 'Member Document Types',
                        'url' => route('vue.member-document-types.index'),
                        'icon' => 'heroicon-o-document-check',
                        'route' => 'vue.member-document-types.index',
                    ],
                    'other-professional-bodies' => [
                        'label' => 'Other Professional Bodies',
                        'url' => route('vue.other-professional-bodies.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.other-professional-bodies.index',
                    ],
                    'professional-memberships' => [
                        'label' => 'Professional Memberships',
                        'url' => route('vue.professional-memberships.index'),
                        'icon' => 'heroicon-o-pencil-square',
                        'route' => 'vue.professional-memberships.index',
                    ],
                    'qualifications' => [
                        'label' => 'Qualifications',
                        'url' => route('vue.qualifications.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.qualifications.index',
                    ],
                    'subjects' => [
                        'label' => 'Subjects',
                        'url' => route('vue.subjects.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.subjects.index',
                    ],
                    'tag-categories' => [
                        'label' => 'Tag Categories',
                        'url' => route('vue.tag-categories.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.tag-categories.index',
                    ],
                    'tags' => [
                        'label' => 'Tags',
                        'url' => route('vue.tags.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.tags.index',
                    ],
                    'types-of-employment' => [
                        'label' => 'Types of Employment',
                        'url' => route('vue.types-of-employment.index'),
                        'icon' => 'heroicon-o-rectangle-stack',
                        'route' => 'vue.types-of-employment.index',
                    ],
                    'type-of-industries' => [
                        'label' => 'Types of Industries',
                        'url' => route('vue.type-of-industries.index'),
                        'icon' => 'heroicon-o-briefcase',
                        'route' => 'vue.type-of-industries.index',
                    ],
                    'unemployment-reasons' => [
                        'label' => 'Unemployment Reasons',
                        'url' => route('vue.unemployment-reasons.index'),
                        'icon' => 'heroicon-o-information-circle',
                        'route' => 'vue.unemployment-reasons.index',
                    ],
                ],
            ],

            'regulatory-reports' => [
                'label' => 'Regulatory Reports',
                'url' => '#',
                'icon' => 'heroicon-o-document-chart-bar',
                'route' => null,
                'children' => [
                    'sars-cpd-verification' => [
                        'label' => 'SARS - CPD Verification Register',
                        'url' => route('vue.reports.sars.cpd-verification'),
                        'icon' => 'heroicon-o-clipboard-document-check',
                        'route' => 'vue.reports.sars.cpd-verification',
                    ],
                    'sars-criminal-check' => [
                        'label' => 'SARS - Criminal Check Verification Report',
                        'url' => route('vue.reports.sars.criminal-check'),
                        'icon' => 'heroicon-o-shield-check',
                        'route' => 'vue.reports.sars.criminal-check',
                    ],
                    'sars-deregistered-tps' => [
                        'label' => 'SARS - Deregistered TPs',
                        'url' => route('vue.reports.sars.deregistered-tps'),
                        'icon' => 'heroicon-o-user-minus',
                        'route' => 'vue.reports.sars.deregistered-tps',
                    ],
                    'sars-new-registered-tp' => [
                        'label' => 'SARS - New Registered TP',
                        'url' => route('vue.reports.sars.new-registered-tps'),
                        'icon' => 'heroicon-o-user-plus',
                        'route' => 'vue.reports.sars.new-registered-tps',
                    ],
                    'sars-tp-good-standing' => [
                        'label' => 'SARS - TP In Good Standing',
                        'url' => route('vue.reports.sars.tp-good-standing'),
                        'icon' => 'heroicon-o-check-circle',
                        'route' => 'vue.reports.sars.tp-good-standing',
                    ],
                    'sars-tp-other-status' => [
                        'label' => 'SARS - TP Other Status Report',
                        'url' => route('vue.reports.sars.tp-other-status'),
                        'icon' => 'heroicon-o-information-circle',
                        'route' => 'vue.reports.sars.tp-other-status',
                    ],
                    'sars-tax-verification' => [
                        'label' => 'SARS - Tax Verification Register Report',
                        'url' => route('vue.reports.sars.tax-compliance'),
                        'icon' => 'heroicon-o-document-check',
                        'route' => 'vue.reports.sars.tax-compliance',
                    ],
                ],
            ],

            'management-tools' => [
                'label' => 'Management Tools',
                'url' => '#',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'route' => null,
                'children' => [
                    // 'import-invoice-form' => [
                    //     'label' => 'Import Invoice Form',
                    //     'url' => route('vue.import-invoice-form'),
                    //     'icon' => 'heroicon-o-arrow-down-tray',
                    //     'route' => 'vue.import-invoice-form',
                    // ],
                    // 'schedule-email-tool' => [
                    //     'label' => 'Schedule Email Tool',
                    //     'url' => route('vue.schedule-email-tool'),
                    //     'icon' => 'heroicon-o-clock',
                    //     'route' => 'vue.schedule-email-tool',
                    // ],
                ],
            ],

            'reporting' => [
                'label' => 'Reporting',
                'url' => '#',
                'icon' => 'heroicon-o-chart-bar',
                'route' => null,
                'children' => [
                    'kpi-report' => [
                        'label' => 'KPI Report',
                        'url' => route('vue.reports.analytics.kpi'),
                        'icon' => 'heroicon-o-chart-bar-square',
                        'route' => 'vue.reports.analytics.kpi',
                    ],
                    'cpd-academy-plan-report' => [
                        'label' => 'CPD Academy Plan Report',
                        'url' => route('vue.reports.member.cpd-academy-plan'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.reports.member.cpd-academy-plan',
                    ],
                    'cpd-academy-sync-report' => [
                        'label' => 'CPD Academy Sync Report',
                        'url' => route('vue.reports.member.cpd-academy-sync'),
                        'icon' => 'heroicon-o-arrow-path',
                        'route' => 'vue.reports.member.cpd-academy-sync',
                    ],
                    'cpd-report' => [
                        'label' => 'CPD Report',
                        'url' => route('vue.reports.member.cpd'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.reports.member.cpd',
                    ],
                    'cpd-time-report' => [
                        'label' => 'CPD Time Report',
                        'url' => route('vue.reports.member.cpd-time'),
                        'icon' => 'heroicon-o-clock',
                        'route' => 'vue.reports.member.cpd-time',
                    ],
                    'channel-two-report' => [
                        'label' => 'Channel Two Report',
                        'url' => route('vue.reports.product.channel-two'),
                        'icon' => 'heroicon-o-chart-pie',
                        'route' => 'vue.reports.product.channel-two',
                    ],
                    'debtors-aging-report' => [
                        'label' => 'Debtors Aging Report',
                        'url' => route('vue.reports.payment.debtors-aging'),
                        'icon' => 'heroicon-o-calendar',
                        'route' => 'vue.reports.payment.debtors-aging',
                    ],
                    'debtors-aging-invoices' => [
                        'label' => 'Debtors Aging Report - Invoices',
                        'url' => route('vue.reports.payment.debtors-aging-invoices'),
                        'icon' => 'heroicon-o-receipt-percent',
                        'route' => 'vue.reports.payment.debtors-aging-invoices',
                    ],
                    'how-did-you-hear' => [
                        'label' => 'How Did You Hear About Us',
                        'url' => route('vue.reports.member.how-did-you-hear'),
                        'icon' => 'heroicon-o-speaker-wave',
                        'route' => 'vue.reports.member.how-did-you-hear',
                    ],
                    'invoice-details-report' => [
                        'label' => 'Invoice Details',
                        'url' => route('vue.reports.payment.invoice-details'),
                        'icon' => 'heroicon-o-document-magnifying-glass',
                        'route' => 'vue.reports.payment.invoice-details',
                    ],
                    'member-products-report' => [
                        'label' => 'Member Products',
                        'url' => route('vue.reports.product.member-products'),
                        'icon' => 'heroicon-o-cube',
                        'route' => 'vue.reports.product.member-products',
                    ],
                    'member-renewals' => [
                        'label' => 'Member Renewals',
                        'url' => route('vue.reports.product.member-renewals'),
                        'icon' => 'heroicon-o-arrow-path',
                        'route' => 'vue.reports.product.member-renewals',
                    ],
                    'not-paid-plus' => [
                        'label' => 'Not Paid For Plus Report',
                        'url' => route('vue.reports.product.not-paid-plus'),
                        'icon' => 'heroicon-o-x-circle',
                        'route' => 'vue.reports.product.not-paid-plus',
                    ],
                    'pi-report' => [
                        'label' => 'PI Report',
                        'url' => route('vue.reports.payment.pi'),
                        'icon' => 'heroicon-o-shield-check',
                        'route' => 'vue.reports.payment.pi',
                    ],
                    'paid-plus' => [
                        'label' => 'Paid For Plus Report',
                        'url' => route('vue.reports.product.paid-plus'),
                        'icon' => 'heroicon-o-check-circle',
                        'route' => 'vue.reports.product.paid-plus',
                    ],
                    'payments-report' => [
                        'label' => 'Payments',
                        'url' => route('vue.reports.payment.payments'),
                        'icon' => 'heroicon-o-banknotes',
                        'route' => 'vue.reports.payment.payments',
                    ],
                    'proforma-invoices-report' => [
                        'label' => 'Proforma Invoices',
                        'url' => route('vue.reports.payment.proforma-invoices'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.reports.payment.proforma-invoices',
                    ],
                    'sales-report' => [
                        'label' => 'Sales Report',
                        'url' => route('vue.reports.sales.sales-report'),
                        'icon' => 'heroicon-o-shopping-cart',
                        'route' => 'vue.reports.sales.sales-report',
                    ],
                    'suspension-status-report' => [
                        'label' => 'Suspension Status Report',
                        'url' => route('vue.reports.member.suspension-status'),
                        'icon' => 'heroicon-o-pause-circle',
                        'route' => 'vue.reports.member.suspension-status',
                    ],
                    'staff-report' => [
                        'label' => 'Staff',
                        'url' => route('vue.reports.staff.staff-report'),
                        'icon' => 'heroicon-o-users',
                        'route' => 'vue.reports.staff.staff-report',
                    ],
                ],
            ],

            'finance' => [
                'label' => 'Finance',
                'url' => '#',
                'icon' => 'heroicon-o-banknotes',
                'route' => null,
                'children' => [
                    'advanced-payment-allocations' => [
                        'label' => 'Advanced Payment Allocations',
                        'url' => route('vue.advanced-payment-allocations.index'),
                        'icon' => 'heroicon-o-calculator',
                        'route' => 'vue.advanced-payment-allocations.index',
                    ],
                    'advanced-payments' => [
                        'label' => 'Advanced Payments',
                        'url' => route('vue.advanced-payments.index'),
                        'icon' => 'heroicon-o-currency-dollar',
                        'route' => 'vue.advanced-payments.index',
                    ],
                    'billing-cycles' => [
                        'label' => 'Billing Cycles',
                        'url' => route('vue.billing-cycles.index'),
                        'icon' => 'heroicon-o-arrow-path',
                        'route' => 'vue.billing-cycles.index',
                    ],
                    'invoices' => [
                        'label' => 'Invoices',
                        'url' => route('vue.invoices.index'),
                        'icon' => 'heroicon-o-receipt-percent',
                        'route' => 'vue.invoices.index',
                    ],
                    'payment-receipts' => [
                        'label' => 'Payment Receipts',
                        'url' => route('vue.payment-receipts.index'),
                        'icon' => 'heroicon-o-document-check',
                        'route' => 'vue.payment-receipts.index',
                    ],
                    'refunds' => [
                        'label' => 'Refunds',
                        'url' => route('vue.refunds.index'),
                        'icon' => 'heroicon-o-arrow-uturn-left',
                        'route' => 'vue.refunds.index',
                    ],
                    'transactions' => [
                        'label' => 'Transactions',
                        'url' => route('vue.transactions.index'),
                        'icon' => 'heroicon-o-list-bullet',
                        'route' => 'vue.transactions.index',
                    ],
                    'proforma-invoices' => [
                        'label' => 'Proforma Invoices',
                        'url' => route('vue.proforma-invoices.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.proforma-invoices.index',
                    ],
                    'debit-orders' => [
                        'label' => 'Debit Orders',
                        'url' => route('vue.debit-orders.index'),
                        'icon' => 'heroicon-o-arrow-down-circle',
                        'route' => 'vue.debit-orders.index',
                    ],
                    'payment-arrangements' => [
                        'label' => 'Payment Arrangements',
                        'url' => route('vue.payment-arrangements.index'),
                        'icon' => 'heroicon-o-document-duplicate',
                        'route' => 'vue.payment-arrangements.index',
                    ],
                    'credit-notes' => [
                        'label' => 'Credit Notes',
                        'url' => route('vue.credit-notes.index'),
                        'icon' => 'heroicon-o-receipt-refund',
                        'route' => 'vue.credit-notes.index',
                    ],
                    'payments' => [
                        'label' => 'Payments',
                        'url' => route('vue.payments.index'),
                        'icon' => 'heroicon-o-credit-card',
                        'route' => 'vue.payments.index',
                    ],
                ],
            ],

            'annual-declarations' => [
                'label' => 'Annual Declarations',
                'url' => '#',
                'icon' => 'heroicon-o-calendar-days',
                'route' => null,
                'children' => [
                    'tax-practitioner-declarations' => [
                        'label' => 'Tax Practitioner Declarations',
                        'url' => route('vue.annual-tax-declarations.index'),
                        'icon' => 'heroicon-o-document-check',
                        'route' => 'vue.annual-tax-declarations.index',
                    ],
                    'annual-tax-declarations' => [
                        'label' => 'Annual Tax Declarations',
                        'url' => route('vue.annual-tax-declarations.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.annual-tax-declarations.index',
                    ],
                    'member-apl-declarations' => [
                        'label' => 'Member Apl Declarations',
                        'url' => route('vue.member-apl-declarations.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.member-apl-declarations.index',
                    ],
                    'member-cpd-declarations' => [
                        'label' => 'Member CPD Declarations',
                        'url' => route('vue.member-c-p-d-declarations.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.member-c-p-d-declarations.index',
                    ],
                    'member-fic-declarations' => [
                        'label' => 'Member Fic Declarations',
                        'url' => route('vue.member-fic-declarations.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.member-fic-declarations.index',
                    ],
                    'member-no-clar-declarations' => [
                        'label' => 'Member No Clar Declarations',
                        'url' => route('vue.member-no-clar-declarations.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.member-no-clar-declarations.index',
                    ],
                ],
            ],

            'member-settings' => [
                'label' => 'Member Settings',
                'url' => '#',
                'icon' => 'heroicon-o-cog',
                'route' => null,
                'children' => [
                    'member-document-types' => [
                        'label' => 'Member Document Types',
                        'url' => route('vue.member-document-types.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.member-document-types.index',
                    ],
                ],
            ],

            'application-setup' => [
                'label' => 'Application Setup',
                'url' => '#',
                'icon' => 'heroicon-o-cog-6-tooth',
                'route' => null,
                'children' => [
                    'promo-codes' => [
                        'label' => 'Promo Codes',
                        'url' => route('vue.application-coupons.index'),
                        'icon' => 'heroicon-o-ticket',
                        'route' => 'vue.application-coupons.index',
                    ],
                    'application-steps' => [
                        'label' => 'Application Steps',
                        'url' => route('vue.application-steps.index'),
                        'icon' => 'heroicon-o-list-bullet',
                        'route' => 'vue.application-steps.index',
                    ],
                    'required-cpd-courses' => [
                        'label' => 'Required C P D Courses',
                        'url' => route('vue.required-c-p-d-courses.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.required-c-p-d-courses.index',
                    ],
                ],
            ],

            'member-applications' => [
                'label' => 'Manage Member Applications',
                'url' => '#',
                'icon' => 'heroicon-o-clipboard-document-list',
                'route' => null,
                'children' => [
                    'application-step-logs' => [
                        'label' => 'Application Step Logs',
                        'url' => route('vue.application-step-logs.index'),
                        'icon' => 'heroicon-o-list-bullet',
                        'route' => 'vue.application-step-logs.index',
                    ],
                    'all-applications' => [
                        'label' => 'All Applications',
                        'url' => route('vue.applications.index'),
                        'icon' => 'heroicon-o-document-duplicate',
                        'route' => 'vue.applications.index',
                    ],
                    'membership-applications' => [
                        'label' => 'Membership Applications',
                        'url' => route('vue.applications.index', ['preset' => 'memberships']),
                        'icon' => 'heroicon-o-user-group',
                        'route' => 'vue.applications.index',
                    ],
                    'designation-applications' => [
                        'label' => 'Designation Applications',
                        'url' => route('vue.applications.index', ['preset' => 'designations']),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.applications.index',
                    ],
                    'licence-applications' => [
                        'label' => 'Licence Applications',
                        'url' => route('vue.applications.index', ['preset' => 'licences']),
                        'icon' => 'heroicon-o-identification',
                        'route' => 'vue.applications.index',
                    ],
                ],
            ],

            'approved-training-offices' => [
                'label' => 'Approved Training Offices',
                'url' => '#',
                'icon' => 'heroicon-o-building-office',
                'route' => null,
                'children' => [
                    'atos' => [
                        'label' => 'Approved Training Offices',
                        'url' => route('vue.approved-training-offices.index'),
                        'icon' => 'heroicon-o-building-office',
                        'route' => 'vue.approved-training-offices.index',
                    ],
                    'ato-compliance' => [
                        'label' => 'Ato Compliance Records',
                        'url' => route('vue.ato-compliance-records.index'),
                        'icon' => 'heroicon-o-check-circle',
                        'route' => 'vue.ato-compliance-records.index',
                    ],
                    'ato-fees' => [
                        'label' => 'Ato Fees',
                        'url' => route('vue.ato-fees.index'),
                        'icon' => 'heroicon-o-currency-dollar',
                        'route' => 'vue.ato-fees.index',
                    ],
                    'ato-trainees' => [
                        'label' => 'Ato Trainees',
                        'url' => route('vue.ato-trainees.index'),
                        'icon' => 'heroicon-o-user-group',
                        'route' => 'vue.ato-trainees.index',
                    ],
                    'training-officers' => [
                        'label' => 'Training Officers',
                        'url' => route('vue.training-officers.index'),
                        'icon' => 'heroicon-o-user',
                        'route' => 'vue.training-officers.index',
                    ],
                ],
            ],

            'administration' => [
                'label' => 'Administration',
                'url' => '#',
                'icon' => 'heroicon-o-cog',
                'route' => null,
                'children' => [
                    'budget-report-months' => [
                        'label' => 'Budget Report Months',
                        'url' => route('vue.budget-report-months.index'),
                        'icon' => 'heroicon-o-calendar',
                        'route' => 'vue.budget-report-months.index',
                    ],
                    'integration-logs' => [
                        'label' => 'Integration Logs',
                        'url' => route('vue.integration-logs.index'),
                        'icon' => 'heroicon-o-server',
                        'route' => 'vue.integration-logs.index',
                    ],
                    'internal-note-reminder-types' => [
                        'label' => 'Internal Note Reminder Types',
                        'url' => route('vue.internal-note-reminder-types.index'),
                        'icon' => 'heroicon-o-bell',
                        'route' => 'vue.internal-note-reminder-types.index',
                    ],
                    'privy-seal-credentials' => [
                        'label' => 'Privy Seal Credentials',
                        'url' => route('vue.privy-seal-credentials.index'),
                        'icon' => 'heroicon-o-finger-print',
                        'route' => 'vue.privy-seal-credentials.index',
                    ],
                    'to-do-lists' => [
                        'label' => 'To Do Lists',
                        'url' => route('vue.to-do-lists.index'),
                        'icon' => 'heroicon-o-queue-list',
                        'route' => 'vue.to-do-lists.index',
                    ],
                    'departments' => [
                        'label' => 'Departments',
                        'url' => route('vue.departments.index'),
                        'icon' => 'heroicon-o-building-office-2',
                        'route' => 'vue.departments.index',
                    ],
                    'roles' => [
                        'label' => 'Roles',
                        'url' => route('vue.roles.index'),
                        'icon' => 'heroicon-o-shield-check',
                        'route' => 'vue.roles.index',
                    ],
                    'greeter-messages' => [
                        'label' => 'Greeter Messages',
                        'url' => route('vue.greeter-messages.index'),
                        'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                        'route' => 'vue.greeter-messages.index',
                    ],
                    // 'member-settings' => [
                    //     'label' => 'Member Settings',
                    //     'url' => route('vue.member-settings.index'),
                    //     'icon' => 'heroicon-o-cog',
                    //     'route' => 'vue.member-settings.index',
                    // ],
                    'payment-gateways' => [
                        'label' => 'Payment Gateways',
                        'url' => route('vue.payment-gateways.index'),
                        'icon' => 'heroicon-o-credit-card',
                        'route' => 'vue.payment-gateways.index',
                    ],
                    'system-configs' => [
                        'label' => 'System Configs',
                        'url' => route('vue.system-configs.index'),
                        'icon' => 'heroicon-o-cog-6-tooth',
                        'route' => 'vue.system-configs.index',
                    ],
                    'countries' => [
                        'label' => 'Countries Management',
                        'url' => route('vue.countries.index'),
                        'icon' => 'heroicon-o-globe-europe-africa',
                        'route' => 'vue.countries.index',
                    ],
                    'user-logs' => [
                        'label' => 'Staff Member Logs (Users)',
                        'url' => route('vue.user-logs.index'),
                        'icon' => 'heroicon-o-clipboard-document-list',
                        'route' => 'vue.user-logs.index',
                    ],
                ],
            ],

            'cpd' => [
                'label' => 'Continuous Professional Development',
                'url' => '#',
                'icon' => 'heroicon-o-academic-cap',
                'route' => null,
                'children' => [
                    'c-p-d-course-points' => [
                        'label' => 'C P D Course Points',
                        'url' => route('vue.c-p-d-course-points.index'),
                        'icon' => 'heroicon-o-star',
                        'route' => 'vue.c-p-d-course-points.index',
                    ],
                    'c-p-d-totals-by-years' => [
                        'label' => 'CPD Totals by Year',
                        'url' => route('vue.c-p-d-totals-by-years.index'),
                        'icon' => 'heroicon-o-chart-bar',
                        'route' => 'vue.c-p-d-totals-by-years.index',
                    ],
                    'certificate-c-p-d-requirements' => [
                        'label' => 'Certificate C P D Requirements',
                        'url' => route('vue.certificate-c-p-d-requirements.index'),
                        'icon' => 'heroicon-o-document-check',
                        'route' => 'vue.certificate-c-p-d-requirements.index',
                    ],
                    'cpd-categories' => [
                        'label' => 'Cpd Categories',
                        'url' => route('vue.cpd-categories.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.cpd-categories.index',
                    ],
                    'cpds' => [
                        'label' => 'Cpds',
                        'url' => route('vue.cpds.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.cpds.index',
                    ],
                    'designation-c-p-d-requirements' => [
                        'label' => 'Designation C P D Requirements',
                        'url' => route('vue.designation-c-p-d-requirements.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.designation-c-p-d-requirements.index',
                    ],
                    'licence-c-p-d-requirements' => [
                        'label' => 'Licence C P D Requirements',
                        'url' => route('vue.licence-c-p-d-requirements.index'),
                        'icon' => 'heroicon-o-clipboard-document',
                        'route' => 'vue.licence-c-p-d-requirements.index',
                    ],
                    'member-cpd-exemption-applications' => [
                        'label' => 'CPD Exemption Applications',
                        'url' => route('vue.member-cpd-exemption-applications.index'),
                        'icon' => 'heroicon-o-shield-check',
                        'route' => 'vue.member-cpd-exemption-applications.index',
                    ],
                    'membership-c-p-d-requirements' => [
                        'label' => 'Membership C P D Requirements',
                        'url' => route('vue.membership-c-p-d-requirements.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.membership-c-p-d-requirements.index',
                    ],
                ],
            ],

            'products' => [
                'label' => 'Products',
                'url' => '#',
                'icon' => 'heroicon-o-cube',
                'route' => null,
                'children' => [
                    'certificates' => [
                        'label' => 'Certificates',
                        'url' => route('vue.certificates.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.certificates.index',
                    ],
                    'designations' => [
                        'label' => 'Designations',
                        'url' => route('vue.designations.index'),
                        'icon' => 'heroicon-o-shield-check',
                        'route' => 'vue.designations.index',
                    ],
                    'licences' => [
                        'label' => 'Licences',
                        'url' => route('vue.licences.index'),
                        'icon' => 'heroicon-o-clipboard-document-check',
                        'route' => 'vue.licences.index',
                    ],
                    'memberships' => [
                        'label' => 'Memberships',
                        'url' => route('vue.memberships.index'),
                        'icon' => 'heroicon-o-user-group',
                        'route' => 'vue.memberships.index',
                    ],
                    'plus-products' => [
                        'label' => 'Plus Products',
                        'url' => route('vue.plus-memberships.index'),
                        'icon' => 'heroicon-o-star',
                        'route' => 'vue.plus-memberships.index',
                    ],
                    'rewards' => [
                        'label' => 'Rewards',
                        'url' => route('vue.rewards.index'),
                        'icon' => 'heroicon-o-gift',
                        'route' => 'vue.rewards.index',
                    ],
                ],
            ],

            'support' => [
                'label' => 'Support',
                'url' => '#',
                'icon' => 'heroicon-o-lifebuoy',
                'route' => null,
                'children' => [
                    'complaints' => [
                        'label' => 'Complaints',
                        'url' => route('vue.complaints.index'),
                        'icon' => 'heroicon-o-exclamation-circle',
                        'route' => 'vue.complaints.index',
                    ],
                    'support-categories' => [
                        'label' => 'Support Categories',
                        'url' => route('vue.support-categories.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.support-categories.index',
                    ],
                    'support-departments' => [
                        'label' => 'Support Departments',
                        'url' => route('vue.support-departments.index'),
                        'icon' => 'heroicon-o-building-office',
                        'route' => 'vue.support-departments.index',
                    ],
                    'support-tickets' => [
                        'label' => 'Support Tickets',
                        'url' => route('vue.support-tickets.index'),
                        'icon' => 'heroicon-o-ticket',
                        'route' => 'vue.support-tickets.index',
                    ],
                ],
            ],

            'corporate-accounts' => [
                'label' => 'Corporate Accounts',
                'url' => '#',
                'icon' => 'heroicon-o-building-office-2',
                'route' => null,
                'children' => [
                    'corporate-accounts' => [
                        'label' => 'Corporate Accounts',
                        'url' => route('vue.corporate-accounts.index'),
                        'icon' => 'heroicon-o-building-office',
                        'route' => 'vue.corporate-accounts.index',
                    ],
                    'corporate-members' => [
                        'label' => 'Corporate Members',
                        'url' => route('vue.corporate-members.index'),
                        'icon' => 'heroicon-o-users',
                        'route' => 'vue.corporate-members.index',
                    ],
                    'corporate-officers' => [
                        'label' => 'Corporate Officers',
                        'url' => route('vue.corporate-officers.index'),
                        'icon' => 'heroicon-o-user',
                        'route' => 'vue.corporate-officers.index',
                    ],
                ],
            ],

            'reporting-targets' => [
                'label' => 'Reporting Targets',
                'url' => '#',
                'icon' => 'heroicon-o-chart-pie',
                'route' => null,
                'children' => [
                    'dashboard-datas' => [
                        'label' => 'Dashboard Datas',
                        'url' => route('vue.dashboard-datas.index'),
                        'icon' => 'heroicon-o-presentation-chart-bar',
                        'route' => 'vue.dashboard-datas.index',
                    ],
                    'reporting-target-categories' => [
                        'label' => 'Reporting Target Categories',
                        'url' => route('vue.reporting-target-categories.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.reporting-target-categories.index',
                    ],
                    'reporting-targets' => [
                        'label' => 'Reporting Targets',
                        'url' => route('vue.reporting-targets.index'),
                        'icon' => 'heroicon-o-flag',
                        'route' => 'vue.reporting-targets.index',
                    ],
                ],
            ],

            'member-assignments' => [
                'label' => 'Member Assignments',
                'url' => '#',
                'icon' => 'heroicon-o-clipboard-document-check',
                'route' => null,
                'children' => [
                    'designation-migrations' => [
                        'label' => 'Designation Migrations',
                        'url' => route('vue.designation-migrations.index'),
                        'icon' => 'heroicon-o-arrow-path',
                        'route' => 'vue.designation-migrations.index',
                    ],
                    'interview-members' => [
                        'label' => 'Schedule Interview',
                        'url' => route('vue.interview-members.index'),
                        'icon' => 'heroicon-o-calendar-days',
                        'route' => 'vue.interview-members.index',
                    ],
                    'member-criminal-checks' => [
                        'label' => 'Member Criminal Checks',
                        'url' => route('vue.member-criminal-checks.index'),
                        'icon' => 'heroicon-o-shield-exclamation',
                        'route' => 'vue.member-criminal-checks.index',
                    ],
                    'member-required-cpd-courses' => [
                        'label' => 'Member Required C P D Courses',
                        'url' => route('vue.member-required-c-p-d-courses.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.member-required-c-p-d-courses.index',
                    ],
                    'member-tags' => [
                        'label' => 'Member Tags',
                        'url' => route('vue.member-tags.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.member-tags.index',
                    ],
                    'online-assessment-members' => [
                        'label' => 'Online Assessment Members',
                        'url' => route('vue.online-assessment-members.index'),
                        'icon' => 'heroicon-o-clipboard-document-check',
                        'route' => 'vue.online-assessment-members.index',
                    ],
                    'payment-extension-plans' => [
                        'label' => 'Payment Extention Plan Applications',
                        'url' => route('vue.payment-extention-plan.index'),
                        'icon' => 'heroicon-o-calendar',
                        'route' => 'vue.payment-extention-plan.index',
                    ],
                    'member-products' => [
                        'label' => 'Member Products',
                        'url' => route('vue.products.index'),
                        'icon' => 'heroicon-o-cube',
                        'route' => 'vue.products.index',
                    ],
                    'tax-assessment-members' => [
                        'label' => 'Tax Assessment Members',
                        'url' => route('vue.tax-assessment-members.index'),
                        'icon' => 'heroicon-o-document-magnifying-glass',
                        'route' => 'vue.tax-assessment-members.index',
                    ],
                    'tax-assessments' => [
                        'label' => 'Tax Assessments',
                        'url' => route('vue.tax-assessments.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.tax-assessments.index',
                    ],
                ],
            ],

            'third-party-interest' => [
                'label' => 'Third Party Interest Forms',
                'url' => '#',
                'icon' => 'heroicon-o-document-duplicate',
                'route' => null,
                'children' => [
                    'draftworx-interest-forms' => [
                        'label' => 'Draftworx Interest Forms',
                        'url' => route('vue.draftworx-interest-forms.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.draftworx-interest-forms.index',
                    ],
                ],
            ],

            'content-management' => [
                'label' => 'Content Management',
                'url' => '#',
                'icon' => 'heroicon-o-book-open',
                'route' => null,
                'children' => [
                    'educational-guide-categories' => [
                        'label' => 'Educational Guide Categories',
                        'url' => route('vue.educational-guide-categories.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.educational-guide-categories.index',
                    ],
                    'educational-guides' => [
                        'label' => 'Educational Guides',
                        'url' => route('vue.educational-guides.index'),
                        'icon' => 'heroicon-o-book-open',
                        'route' => 'vue.educational-guides.index',
                    ],
                ],
            ],

            'communication' => [
                'label' => 'Communication',
                'url' => '#',
                'icon' => 'heroicon-o-envelope',
                'route' => null,
                'children' => [
                    'email-logs' => [
                        'label' => 'Email Logs',
                        'url' => route('vue.email-logs.index'),
                        'icon' => 'heroicon-o-clipboard-document-list',
                        'route' => 'vue.email-logs.index',
                    ],
                    'email-templates' => [
                        'label' => 'Email Templates',
                        'url' => route('vue.email-templates.index'),
                        'icon' => 'heroicon-o-envelope',
                        'route' => 'vue.email-templates.index',
                    ],
                    's-m-s-messages' => [
                        'label' => 'S M S Messages',
                        'url' => route('vue.s-m-s-messages.index'),
                        'icon' => 'heroicon-o-chat-bubble-left-right',
                        'route' => 'vue.s-m-s-messages.index',
                    ],
                    'sms-templates' => [
                        'label' => 'SMS Templates',
                        'url' => route('vue.sms-templates.index'),
                        'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                        'route' => 'vue.sms-templates.index',
                    ],
                    'text-messages' => [
                        'label' => 'Text Messages',
                        'url' => route('vue.text-messages.index'),
                        'icon' => 'heroicon-o-paper-airplane',
                        'route' => 'vue.text-messages.index',
                    ],
                ],
            ],

            'product-requirements' => [
                'label' => 'Product Requirements',
                'url' => '#',
                'icon' => 'heroicon-o-clipboard-document-list',
                'route' => null,
                'children' => [
                    'external-companies' => [
                        'label' => 'Reward External Partners',
                        'url' => route('vue.external-companies.index'),
                        'icon' => 'heroicon-o-building-office',
                        'route' => 'vue.external-companies.index',
                    ],
                    'interviews' => [
                        'label' => 'Interviews',
                        'url' => route('vue.interviews.index'),
                        'icon' => 'heroicon-o-user-circle',
                        'route' => 'vue.interviews.index',
                    ],
                    'online-assessments' => [
                        'label' => 'Online Assessments',
                        'url' => route('vue.online-assessments.index'),
                        'icon' => 'heroicon-o-clipboard-document-check',
                        'route' => 'vue.online-assessments.index',
                    ],
                ],
            ],

            'manage-members' => [
                'label' => 'Manage Members',
                'url' => '#',
                'icon' => 'heroicon-o-user-group',
                'route' => null,
                'children' => [
                    'internal-notes' => [
                        'label' => 'Internal Notes',
                        'url' => route('vue.internal-notes.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.internal-notes.index',
                    ],
                    'member-certificates' => [
                        'label' => 'Member Certificates',
                        'url' => route('vue.member-certificates.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.member-certificates.index',
                    ],
                    'member-document-requests' => [
                        'label' => 'Member Document Requests',
                        'url' => route('vue.member-document-requests.index'),
                        'icon' => 'heroicon-o-document-arrow-up',
                        'route' => 'vue.member-document-requests.index',
                    ],
                    'member-employment-histories' => [
                        'label' => 'Member Employment Histories',
                        'url' => route('vue.member-employment-histories.index'),
                        'icon' => 'heroicon-o-briefcase',
                        'route' => 'vue.member-employment-histories.index',
                    ],
                    'member-p-r-number-requests' => [
                        'label' => 'Member P R Number Requests',
                        'url' => route('vue.member-p-r-number-requests.index'),
                        'icon' => 'heroicon-o-hashtag',
                        'route' => 'vue.member-p-r-number-requests.index',
                    ],
                    'user-member-logs' => [
                        'label' => 'User Member Logs',
                        'url' => \Illuminate\Support\Facades\Route::has('vue.user-member-logs.index')
                            ? route('vue.user-member-logs.index')
                            : '#',
                        'icon' => 'heroicon-o-clipboard-document-list',
                        'route' => \Illuminate\Support\Facades\Route::has('vue.user-member-logs.index')
                            ? 'vue.user-member-logs.index'
                            : null,
                    ],
                    'member-logs' => [
                        'label' => 'Member Logs',
                        'url' => route('vue.member-logs.index'),
                        'icon' => 'heroicon-o-clipboard-document',
                        'route' => 'vue.member-logs.index',
                    ],
                    'member-status-logs' => [
                        'label' => 'Member Status Logs',
                        'url' => route('vue.member-status-logs.index'),
                        'icon' => 'heroicon-o-clock',
                        'route' => 'vue.member-status-logs.index',
                    ],
                    'member-documents' => [
                        'label' => 'Member Documents',
                        'url' => route('vue.member-documents.index'),
                        'icon' => 'heroicon-o-folder-open',
                        'route' => 'vue.member-documents.index',
                    ],
                    'members' => [
                        'label' => 'Members',
                        'url' => route('vue.members.index'),
                        'icon' => 'heroicon-o-user-group',
                        'route' => 'vue.members.index',
                    ],
                ],
            ],

            'cpd-academy' => [
                'label' => 'CPD Academy',
                'url' => '#',
                'icon' => 'heroicon-o-academic-cap',
                'route' => null,
                'children' => [
                    'academy-statuses' => [
                        'label' => 'Academy Statuses',
                        'url' => route('vue.member-academy-statuses.index'),
                        'icon' => 'heroicon-o-flag',
                        'route' => 'vue.member-academy-statuses.index',
                    ],
                ],
            ],

            'member-management' => [
                'label' => 'Member Management',
                'url' => '#',
                'icon' => 'heroicon-o-users',
                'route' => null,
                'children' => [
                    'account-issues' => [
                        'label' => 'Account Issues',
                        'url' => route('vue.member-account-issues.index'),
                        'icon' => 'heroicon-o-exclamation-triangle',
                        'route' => 'vue.member-account-issues.index',
                    ],
                ],
            ],

            'member-pages' => [
                'label' => 'Member Pages',
                'url' => '#',
                'icon' => 'heroicon-o-document-duplicate',
                'route' => null,
                'children' => [
                    'member-banners' => [
                        'label' => 'Member Banners',
                        'url' => route('vue.member-banners.index'),
                        'icon' => 'heroicon-o-photo',
                        'route' => 'vue.member-banners.index',
                    ],
                    'member-page-menus' => [
                        'label' => 'Member Page Menus',
                        'url' => route('vue.member-page-menus.index'),
                        'icon' => 'heroicon-o-bars-3',
                        'route' => 'vue.member-page-menus.index',
                    ],
                    'member-pages' => [
                        'label' => 'Member Pages',
                        'url' => route('vue.member-pages.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.member-pages.index',
                    ],
                    'member-page-sections' => [
                        'label' => 'Member Page Sections',
                        'url' => route('vue.member-page-sections.index'),
                        'icon' => 'heroicon-o-rectangle-group',
                        'route' => 'vue.member-page-sections.index',
                    ],
                ],
            ],

            'suspended-members' => [
                'label' => 'Suspended Members',
                'url' => '#',
                'icon' => 'heroicon-o-pause-circle',
                'route' => null,
                'children' => [
                    'members-not-good-standing' => [
                        'label' => 'Members NOT in Good Standing',
                        'url' => route('vue.member-contacts.index'),
                        'icon' => 'heroicon-o-user-minus',
                        'route' => 'vue.member-contacts.index',
                    ],
                    'suspension-scan-results' => [
                        'label' => 'Suspension Scan Results',
                        'url' => route('vue.member-suspension-scans.index'),
                        'icon' => 'heroicon-o-magnifying-glass',
                        'route' => 'vue.member-suspension-scans.index',
                    ],
                    'contact-templates' => [
                        'label' => 'Contact Templates',
                        'url' => route('vue.note-templates.index'),
                        'icon' => 'heroicon-o-document-text',
                        'route' => 'vue.note-templates.index',
                    ],
                    // 'waiting-list' => [
                    //     'label' => 'Waiting List',
                    //     'url' => \Illuminate\Support\Facades\Route::has('vue.member-waiting-lists.index')
                    //         ? route('vue.member-waiting-lists.index')
                    //         : '#',
                    //     'icon' => 'heroicon-o-queue-list',
                    //     'route' => \Illuminate\Support\Facades\Route::has('vue.member-waiting-lists.index')
                    //         ? 'vue.member-waiting-lists.index'
                    //         : null,
                    // ],
                    'member-risk-rankings' => [
                        'label' => 'Member Risk Rankings',
                        'url' => route('vue.member-risk-rankings.index'),
                        'icon' => 'heroicon-o-shield-exclamation',
                        'route' => 'vue.member-risk-rankings.index',
                    ],
                ],
            ],
            'system-logs' => [
                'label' => 'System Logs',
                'url' => '#',
                'icon' => 'heroicon-o-server',
                'route' => null,
                'children' => [
                    'id-number-verifications' => [
                        'label' => 'ID Number Verifications',
                        'url' => route('vue.i-d-number-verifications.index'),
                        'icon' => 'heroicon-o-identification',
                        'route' => 'vue.i-d-number-verifications.index',
                    ],
                    'mie-queries' => [
                        'label' => 'Mie Queries',
                        'url' => route('vue.mie-queries.index'),
                        'icon' => 'heroicon-o-magnifying-glass',
                        'route' => 'vue.mie-queries.index',
                    ],
                ],
            ],
            'system-logs' => [
                'label' => 'System Logs',
                'url' => '#',
                'icon' => 'heroicon-o-server',
                'route' => null,
                'children' => [
                    'payfast-logs' => [
                        'label' => 'Payfast Logs',
                        'url' => route('vue.payfast-logs.index'),
                        'icon' => 'heroicon-o-credit-card',
                        'route' => 'vue.payfast-logs.index',
                    ],
                    'payment-allocation-traces' => [
                        'label' => 'Payment Allocation Traces',
                        'url' => route('vue.payment-allocation-traces.index'),
                        'icon' => 'heroicon-o-arrow-path',
                        'route' => 'vue.payment-allocation-traces.index',
                    ],
                    'scheduled-job-logs' => [
                        'label' => 'Scheduled Job Logs',
                        'url' => route('vue.scheduled-job-logs.index'),
                        'icon' => 'heroicon-o-clock',
                        'route' => 'vue.scheduled-job-logs.index',
                    ],
                ],
            ],
            'invoice-assignments' => [
                'label' => 'Invoice Assignments',
                'url' => '#',
                'icon' => 'heroicon-o-document-arrow-down',
                'route' => null,
                'children' => [
                    'proforma-tags' => [
                        'label' => 'Proforma Tags',
                        'url' => route('vue.proforma-tags.index'),
                        'icon' => 'heroicon-o-tag',
                        'route' => 'vue.proforma-tags.index',
                    ],
                ],
            ],

            'staff-management' => [
                'label' => 'Staff Management',
                'url' => '#',
                'icon' => 'heroicon-o-users',
                'route' => null,
                'children' => [
                    'staff-team-designations' => [
                        'label' => 'Designation Application Teams',
                        'url' => route('vue.staff-team-designations.index'),
                        'icon' => 'heroicon-o-academic-cap',
                        'route' => 'vue.staff-team-designations.index',
                    ],
                    'staff-team-licences' => [
                        'label' => 'Licence Applications Teams',
                        'url' => route('vue.staff-team-licences.index'),
                        'icon' => 'heroicon-o-clipboard-document-check',
                        'route' => 'vue.staff-team-licences.index',
                    ],
                    'staff-team-memberships' => [
                        'label' => 'Membership Application Teams',
                        'url' => route('vue.staff-team-memberships.index'),
                        'icon' => 'heroicon-o-puzzle-piece',
                        'route' => 'vue.staff-team-memberships.index',
                    ],
                    'users' => [
                        'label' => 'Staff Members (Users)',
                        'url' => route('vue.users.index'),
                        'icon' => 'heroicon-o-user',
                        'route' => 'vue.users.index',
                    ],
                    'staff-team-members' => [
                        'label' => 'Staff Team Members',
                        'url' => route('vue.staff-team-members.index'),
                        'icon' => 'heroicon-o-users',
                        'route' => 'vue.staff-team-members.index',
                    ],
                    'staff-teams' => [
                        'label' => 'Staff Teams',
                        'url' => route('vue.staff-teams.index'),
                        'icon' => 'heroicon-o-user-group',
                        'route' => 'vue.staff-teams.index',
                    ],
                ],
            ],

            'forms' => [
                'label' => 'Forms',
                'url' => '#',
                'icon' => 'heroicon-o-document',
                'route' => null,
                'children' => [
                    'form-items' => [
                        'label' => 'Form Items',
                        'url' => route('vue.form-items.index'),
                        'icon' => 'heroicon-o-document-magnifying-glass',
                        'route' => 'vue.form-items.index',
                    ],
                    'forms' => [
                        'label' => 'Forms',
                        'url' => route('vue.forms.index'),
                        'icon' => 'heroicon-o-document',
                        'route' => 'vue.forms.index',
                    ],
                ],
            ],
        ];
    }

    public function getMenu(?string $currentRoute = null, $user = null): array
    {
        // Load user permissions once at the start to avoid repeated queries
        if ($user && $this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Get menu from database instead of hardcoded array
        $menu = $this->getMenuFromDatabase($user);

        if ($currentRoute) {
            $this->markActiveItems($menu, $currentRoute);
        }

        if ($user) {
            $favoriteKeys = $user->getFavoriteMenuKeys();
            $this->markFavoritedItems($menu, $favoriteKeys);
        }

        // Clear cache after use to avoid stale data
        $this->cachedUserPermissions = null;
        $this->debugLogged = false;

        return $menu;
    }

    public function getFavoriteMenuItems($user): array
    {
        if (! $user) {
            return [];
        }

        // Load user permissions once at the start to avoid repeated queries
        if ($this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Use database menu instead of hardcoded array
        $menu = $this->getMenuFromDatabase($user);
        $favoriteMenuItems = $user->favoriteMenuItems()->orderBy('order')->get();
        $result = [];

        foreach ($favoriteMenuItems as $favorite) {
            $menuItem = $this->findMenuItemByKey($menu, $favorite->menu_key);
            if ($menuItem && $this->canAccessMenuItem($menuItem, $user)) {
                $menuItem['menu_key'] = $favorite->menu_key;
                $menuItem['order'] = $favorite->order;
                $result[] = $menuItem;
            }
        }

        // Clear cache after use to avoid stale data
        $this->cachedUserPermissions = null;
        $this->debugLogged = false;

        return $result;
    }

    private function findMenuItemByKey(array $menu, string $key): ?array
    {
        foreach ($menu as $menuKey => $item) {
            if ($menuKey === $key) {
                return $item;
            }

            if (isset($item['children'])) {
                $found = $this->findMenuItemByKey($item['children'], $key);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function markFavoritedItems(array &$menu, array $favoriteKeys): void
    {
        foreach ($menu as $key => &$item) {
            if (in_array($key, $favoriteKeys)) {
                $item['is_favorited'] = true;
            }

            if (isset($item['children'])) {
                $this->markFavoritedItems($item['children'], $favoriteKeys);
            }
        }
    }

    private function markActiveItems(array &$menu, string $currentRoute): void
    {
        $currentUrl = request()->fullUrl();

        foreach ($menu as $key => &$item) {
            $item['active'] = false;

            // Check if URLs match exactly (including query parameters)
            if (isset($item['url']) && $this->isUrlMatch($item['url'], $currentUrl)) {
                $item['active'] = true;
            }
            // Fallback to route matching if no exact URL match
            elseif (isset($item['route']) && $this->isRouteMatch($item['route'], $currentRoute)) {
                // Only mark as active if no query parameters in current URL
                // This prevents "All Applications" from being active when filtered
                if (! request()->hasAny(['subscribable_type', 'progress', 'assigned_to_me'])) {
                    $item['active'] = true;
                }
            }

            if (isset($item['children'])) {
                $this->markActiveItems($item['children'], $currentRoute);

                $hasActiveChild = $this->hasActiveChild($item['children']);
                if ($hasActiveChild) {
                    $item['active'] = true;
                }
            }
        }
    }

    private function isUrlMatch(string $menuUrl, string $currentUrl): bool
    {
        // Normalize URLs for comparison
        $menuUrl = rtrim($menuUrl, '?');
        $currentUrl = rtrim($currentUrl, '?');

        return $menuUrl === $currentUrl;
    }

    private function isRouteMatch(string $menuRoute, string $currentRoute): bool
    {
        if ($menuRoute === $currentRoute) {
            return true;
        }

        $menuRouteParts = explode('.', $menuRoute);
        $currentRouteParts = explode('.', $currentRoute);

        if (count($menuRouteParts) < count($currentRouteParts)) {
            $menuRoutePrefix = implode('.', $menuRouteParts);
            $currentRoutePrefix = implode('.', array_slice($currentRouteParts, 0, count($menuRouteParts)));

            if ($menuRoutePrefix === $currentRoutePrefix) {
                return true;
            }
        }

        return false;
    }

    private function hasActiveChild(array $children): bool
    {
        foreach ($children as $child) {
            if (isset($child['active']) && $child['active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter menu items based on user permissions
     */
    private function filterMenuByPermissions(array $menu, $user): array
    {
        $filtered = [];

        foreach ($menu as $key => $item) {
            // Check if user can access this item
            if (! $this->canAccessMenuItem($item, $user)) {
                continue;
            }

            // If item has children, filter them recursively
            if (isset($item['children']) && is_array($item['children'])) {
                $filteredChildren = $this->filterMenuByPermissions($item['children'], $user);

                // Only include item if it has visible children or no route (group header)
                if (empty($filteredChildren) && isset($item['route']) && $item['route'] !== null) {
                    continue;
                }

                $item['children'] = $filteredChildren;
            }

            $filtered[$key] = $item;
        }

        return $filtered;
    }

    /**
     * Check if user can access a menu item based on permissions
     */
    private function canAccessMenuItem(array $item, $user): bool
    {
        // If no route specified, allow access (for group headers)
        if (! isset($item['route']) || $item['route'] === null) {
            return true;
        }

        // Check if user is a Member (members don't have admin permissions)
        if ($user instanceof \App\Models\Member) {
            return false;
        }

        // Check if user implements Authorizable
        if (! $user instanceof \Illuminate\Contracts\Auth\Access\Authorizable) {
            return false;
        }

        $route = $item['route'];

        // Special cases that don't follow standard resource pattern
        if ($this->isSpecialRoute($route)) {
            return $this->canAccessSpecialRoute($route, $user);
        }

        // Standard resource routes: vue.{resource}.index
        return $this->canAccessRoute($route, $user);
    }

    /**
     * Get user permissions list (cached for performance)
     */
    private function getUserPermissionsList($user)
    {
        // Use cached permissions if available, otherwise load once
        // Use same cache duration logic as Navigation for consistency
        $cacheTime = config('app.debug') ? 60 * 5 : 60 * 60; // 5 minutes in debug, 1 hour in production
        return Cache::remember('user_permissions_'.$user->id, $cacheTime, function () use ($user) {
            return $user->getAllPermissions()->pluck('name')->toArray();
        });
    }

    /**
     * Check if user has a specific permission (using cached permissions array)
     */
    private function userHasPermission($user, string $permission): bool
    {
        // Ensure permissions are loaded
        if ($this->cachedUserPermissions === null && $user) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        if ($this->cachedUserPermissions === null) {
            return false;
        }

        return in_array($permission, $this->cachedUserPermissions, true);
    }

    /**
     * Check if user can access a standard resource route
     */
    private function canAccessRoute(?string $route, $user): bool
    {
        if (! $route) {
            return true;
        }

        // Extract resource name from route (e.g., vue.roles.index -> roles)
        $routeParts = explode('.', $route);
        if (count($routeParts) < 3 || $routeParts[0] !== 'vue') {
            // Not a standard vue-admin route, allow by default
            return true;
        }

        $resourceName = $routeParts[1];

        // Convert resource name to model name (singular)
        // e.g., roles -> Role, application-decline-reasons -> ApplicationDeclineReason
        // proforma-invoices -> ProformaInvoices -> ProformaInvoice
        $modelName = $this->routeToModelName($resourceName);

        // Get both singular and plural snake_case versions
        // Singular: ProformaInvoice -> proforma_invoice
        // Plural: proforma-invoices -> ProformaInvoices -> proforma_invoices
        $snakeCaseSingular = \Illuminate\Support\Str::snake($modelName);
        $words = explode('-', $resourceName);
        $pascalCasePlural = implode('', array_map('ucfirst', $words));
        $snakeCasePlural = \Illuminate\Support\Str::snake($pascalCasePlural);

        // Generate permission variations for both singular and plural
        // Filament uses :: instead of _ for multi-word model names
        $permissions = array_merge(
            $this->generatePermissionVariations($snakeCaseSingular),
            $this->generatePermissionVariations($snakeCasePlural)
        );

        // Debug logging when APP_DEBUG is enabled (only log first occurrence per request)
        if (config('app.debug') && ! $this->debugLogged) {
            $this->debugLogged = true;
            // Ensure permissions are loaded
            if ($this->cachedUserPermissions === null) {
                $this->cachedUserPermissions = $this->getUserPermissionsList($user);
            }
            $userPermissions = $this->cachedUserPermissions;
            $userRoles = $user->getRoleNames()->toArray();

            // Only load role details if roles are already loaded (avoid N+1)
            $roleDetails = [];
            if ($user->relationLoaded('roles')) {
                foreach ($user->roles as $role) {
                    $roleDetails[] = [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => $role->relationLoaded('permissions')
                            ? $role->permissions->pluck('name')->toArray()
                            : [],
                        'permissions_count' => $role->relationLoaded('permissions')
                            ? $role->permissions->count()
                            : 0,
                    ];
                }
            }

            \Illuminate\Support\Facades\Log::debug('MenuWebService: Permission check optimization', [
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
                'user_roles' => $userRoles,
                'role_details' => $roleDetails,
                'user_permissions_count' => count($userPermissions),
                'permissions_cached' => $this->cachedUserPermissions !== null,
            ]);
        }

        // Check if user has any of these permissions using cached list
        foreach ($permissions as $permission) {
            if ($this->userHasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate permission variations for a given snake_case string
     * Returns both underscore and double-colon formats
     * e.g., proforma_invoice -> ['view_any_proforma_invoice', 'view_any_proforma::invoice']
     */
    private function generatePermissionVariations(string $snakeCase): array
    {
        return [
            'view_any_'.$snakeCase, // underscore format
            'view_any_'.str_replace('_', '::', $snakeCase), // double-colon format (Filament)
        ];
    }

    /**
     * Convert route resource name to model name
     * e.g., roles -> Role, application-decline-reasons -> ApplicationDeclineReason
     */
    private function routeToModelName(string $resourceName): string
    {
        // Convert kebab-case to PascalCase
        $words = explode('-', $resourceName);
        $pascalCase = implode('', array_map('ucfirst', $words));

        // Handle plural to singular conversion for common cases
        $singular = $this->pluralToSingular($pascalCase);

        return $singular;
    }

    /**
     * Convert plural model name to singular
     */
    private function pluralToSingular(string $name): string
    {
        // Handle words ending in 'ies' -> 'y' (e.g., Countries -> Country)
        if (preg_match('/ies$/i', $name)) {
            return preg_replace('/ies$/i', 'y', $name);
        }

        // Handle words ending in 'ses' -> 's' (e.g., Classes -> Class)
        if (preg_match('/ses$/i', $name)) {
            return preg_replace('/ses$/i', 's', $name);
        }

        // Handle words ending in 'ches' -> 'ch' (e.g., Branches -> Branch)
        if (preg_match('/ches$/i', $name)) {
            return preg_replace('/ches$/i', 'ch', $name);
        }

        // Handle words ending in 'shes' -> 'sh' (e.g., Dishes -> Dish)
        if (preg_match('/shes$/i', $name)) {
            return preg_replace('/shes$/i', 'sh', $name);
        }

        // Handle words ending in 'xes' -> 'x' (e.g., Boxes -> Box)
        if (preg_match('/xes$/i', $name)) {
            return preg_replace('/xes$/i', 'x', $name);
        }

        // Handle words ending in 'zes' -> 'z' (e.g., Quizzes -> Quiz)
        if (preg_match('/zes$/i', $name)) {
            return preg_replace('/zes$/i', 'z', $name);
        }

        // Default: remove 's' at the end if present
        if (substr($name, -1) === 's' && strlen($name) > 1) {
            return substr($name, 0, -1);
        }

        return $name;
    }

    /**
     * Check if route is a special case (dashboards, reports, etc.)
     */
    private function isSpecialRoute(string $route): bool
    {
        $specialPrefixes = [
            'vue.dashboard',
            'vue.dashboards.',
            'vue.reports.',
        ];

        foreach ($specialPrefixes as $prefix) {
            if (str_starts_with($route, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access special routes (dashboards, reports)
     */
    private function canAccessSpecialRoute(string $route, $user): bool
    {
        // Dashboard routes - allow if user is authenticated
        if (str_starts_with($route, 'vue.dashboard') || str_starts_with($route, 'vue.dashboards.')) {
            return true;
        }

        // Report routes - check for specific report permissions
        if (str_starts_with($route, 'vue.reports.')) {
            // Extract report name from route
            // e.g., vue.reports.sars.cpd-verification -> sars_cpd_verification
            $routeParts = explode('.', $route);
            if (count($routeParts) >= 3) {
                $reportParts = array_slice($routeParts, 2);
                $reportName = implode('_', $reportParts);
                $permission = 'view_any_report_'.$reportName;

                // Try both formats
                $permissionFilament = 'view_any_report::'.str_replace('_', '::', $reportName);

                return $this->userHasPermission($user, $permission) || $this->userHasPermission($user, $permissionFilament);
            }

            // Default: allow if user can view any report
            return $this->userHasPermission($user, 'view_any_report');
        }

        // Default: allow access for unknown special routes
        return true;
    }

    /**
     * Get menu from database, grouped by MenuGroup and filtered by user permissions
     */
    private function getMenuFromDatabase($user = null): array
    {
        // Load user permissions if not already loaded
        if ($user && $this->cachedUserPermissions === null) {
            $this->cachedUserPermissions = $this->getUserPermissionsList($user);
        }

        // Query active MenuGroups ordered by sort_order
        $menuGroups = MenuGroup::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $menu = [];

        foreach ($menuGroups as $group) {
            // Build permission filter for MenuItems
            $menuItemsQuery = MenuItem::where('menu_group_id', $group->id)
                ->where('is_active', true)
                ->where(function ($query) use ($user) {
                    // Show items with no permission requirement (accessible to all)
                    $query->whereNull('permission_name');

                    // OR show items where user has the required permission
                    if ($user && $this->cachedUserPermissions !== null && ! empty($this->cachedUserPermissions)) {
                        $query->orWhereIn('permission_name', $this->cachedUserPermissions);
                    }
                })
                ->orderBy('sort_order')
                ->get();

            // Build nested structure for menu items
            $children = $this->buildMenuItemTree($menuItemsQuery, $user);

            // Only include group if it has accessible children
            if (! empty($children)) {
                $menu[$group->key] = [
                    'label' => $group->label,
                    'url' => '#',
                    'icon' => $group->icon,
                    'route' => null,
                    'children' => $children,
                ];
            }
        }

        return $menu;
    }

    /**
     * Build nested tree structure for menu items, handling parent_id relationships
     */
    private function buildMenuItemTree($menuItems, $user = null): array
    {
        $tree = [];
        $itemsByParent = [];

        // Group items by parent_id
        foreach ($menuItems as $item) {
            $parentId = $item->parent_id ?? 'root';
            if (! isset($itemsByParent[$parentId])) {
                $itemsByParent[$parentId] = [];
            }
            $itemsByParent[$parentId][] = $item;
        }

        // Build tree starting from root items (parent_id is null)
        if (isset($itemsByParent['root'])) {
            foreach ($itemsByParent['root'] as $item) {
                $menuItemData = $this->transformMenuItemToArray($item);

                // Recursively add children
                if (isset($itemsByParent[$item->id])) {
                    $menuItemData['children'] = $this->buildChildrenTree($itemsByParent[$item->id], $itemsByParent, $user);
                }

                $tree[$item->key] = $menuItemData;
            }
        }

        return $tree;
    }

    /**
     * Recursively build children tree for nested menu items
     */
    private function buildChildrenTree($children, &$itemsByParent, $user = null): array
    {
        $tree = [];

        foreach ($children as $item) {
            $menuItemData = $this->transformMenuItemToArray($item);

            // Recursively add nested children
            if (isset($itemsByParent[$item->id])) {
                $menuItemData['children'] = $this->buildChildrenTree($itemsByParent[$item->id], $itemsByParent, $user);
            }

            $tree[$item->key] = $menuItemData;
        }

        return $tree;
    }

    /**
     * Transform MenuItem model to array format matching getMenuArray() structure
     */
    private function transformMenuItemToArray(MenuItem $item): array
    {
        $data = [
            'label' => $item->label,
            'icon' => $item->icon,
            'route' => $item->route,
        ];

        // Set URL - use route() helper if route exists, otherwise use stored URL
        if ($item->route && \Illuminate\Support\Facades\Route::has($item->route)) {
            $data['url'] = route($item->route);
        } else {
            $data['url'] = $item->url ?? '#';
        }

        return $data;
    }
}
