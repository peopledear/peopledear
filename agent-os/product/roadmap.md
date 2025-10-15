# PeopleDear Product Roadmap

## Current State Assessment

### Implemented Features
- ✅ User authentication (login/logout)
- ✅ User profile management (email, name, avatar)

### To Be Built
Everything else in the roadmap below needs to be implemented from scratch:
- User and employee management (admin capabilities)
- Overtime registration and calculation engine
- Time-off request system
- Holiday management
- Approval workflows
- Expense tracking
- Business trip planning
- Document upload and management
- Reporting and data export

---

## Development Phases

## Phase 1: MVP Foundation (Months 1-3)
**Goal**: Deliver a fully functional single-tenant application for the existing customer

### 1.1 User & Company Management
**Priority**: Critical
**Timeline**: Week 1-2

- [ ] Company account creation and configuration
- [ ] Employee onboarding with role-based access control
- [ ] Admin panel for user management
- [ ] Approval hierarchy setup (multi-level workflows)
- [ ] Permission system (admin, manager, employee roles)

**Success Criteria**:
- Ability to create company with multiple employees
- Role-based permissions working correctly
- Approval chains configurable per company

---

### 1.2 Core Time Tracking System
**Priority**: Critical
**Timeline**: Week 3-6

#### Overtime Registration
- [ ] Overtime request form with date, hours, and justification
- [ ] Portuguese overtime calculation engine:
  - Weekday overtime: First hour +50%, subsequent hours +100%
  - Saturday work: Double pay rate
  - Sunday work: 2 compensatory days off tracking
  - Holiday work: Separate calculation rules
- [ ] Overtime approval workflow
- [ ] Historical overtime tracking and reporting

#### Time-Off Requests
- [ ] Vacation request system
- [ ] Sick leave tracking
- [ ] Personal days management
- [ ] Automatic balance calculation (accrued days, used days, remaining)
- [ ] Calendar integration showing approved time-off
- [ ] Conflict detection with holidays and other employees

#### Holiday Management Enhancement
- [ ] Integration of national, regional, and local holidays
- [ ] Automatic conflict checking for time-off requests
- [ ] Holiday work tracking and compensation calculation

**Success Criteria**:
- 95%+ accuracy in overtime calculations vs. manual review
- Zero conflicts in holiday/time-off approvals
- All time tracking data exportable for payroll

**Dependencies**: User & Company Management (1.1)

---

### 1.3 Approval Workflows & Notifications
**Priority**: Critical
**Timeline**: Week 7-8

- [ ] Multi-step approval chains (configurable per request type)
- [ ] Email notifications for:
  - New requests submitted
  - Approvals granted/denied
  - Escalations (overdue approvals)
- [ ] Mobile-friendly approval interface
- [ ] Approval history and audit trail
- [ ] Notification preferences per user

**Success Criteria**:
- All approval workflows function end-to-end
- Email notifications sent within 2 minutes of events
- Mobile approval interface tested on iOS and Android

**Dependencies**: Core Time Tracking System (1.2)

---

### 1.4 Document Management
**Priority**: High
**Timeline**: Week 9-10

- [ ] Document upload for time-off justifications (medical certificates, etc.)
- [ ] Document upload for expense reports (receipts, invoices)
- [ ] Secure file storage (Hetzner S3-compatible buckets)
- [ ] Document viewer within application
- [ ] Document versioning and audit trail

**Success Criteria**:
- Users can upload and view documents
- All documents securely stored and accessible only to authorized users
- Document audit trail complete

**Dependencies**: Core Time Tracking System (1.2)

---

### 1.5 Reporting & Data Export
**Priority**: High
**Timeline**: Week 11-12

- [ ] Manager dashboard with:
  - Pending approvals summary
  - Overtime trends
  - Time-off calendar
  - Expense summary
- [ ] Payroll export functionality:
  - CSV/Excel export of overtime hours
  - Time-off summary export
  - Expense report export
- [ ] Custom date range reporting
- [ ] Employee self-service reports (own time tracking, balances)

**Success Criteria**:
- Managers can view real-time approval status
- Export format compatible with common payroll processors
- Reports accurate and match individual records

**Dependencies**: All previous features (1.1-1.4)

---

### Phase 1 Testing & Deployment
**Timeline**: Week 13

- [ ] End-to-end testing with existing customer
- [ ] Performance testing (page load times, database queries)
- [ ] Security audit (authentication, authorization, data access)
- [ ] Deploy to production on Hetzner VPS via Laravel Forge
- [ ] Customer training and onboarding
- [ ] Monitoring setup with Laravel Nightwatch

**Success Criteria**:
- All features working in production
- Customer successfully using application daily
- No critical bugs in first 2 weeks of production use

---

## Phase 2: Multi-Tenancy & SaaS Launch (Months 4-6)
**Goal**: Transform single-tenant application into scalable SaaS platform

### 2.1 Multi-Tenancy Architecture
**Priority**: Critical
**Timeline**: Week 14-16

- [ ] Database schema refactoring for multi-tenancy
- [ ] Tenant isolation and security
- [ ] Subdomain or path-based tenant routing
- [ ] Tenant-specific configuration and branding
- [ ] Data migration from single-tenant to multi-tenant

**Success Criteria**:
- Multiple companies can use platform simultaneously
- Complete data isolation between tenants
- Existing customer migrated without downtime

---

### 2.2 Subscription & Billing
**Priority**: Critical
**Timeline**: Week 17-19

- [ ] Subscription plan creation (tiered pricing)
- [ ] Stripe integration for payments (or Easypay for Portuguese market)
- [ ] Invoice generation and email delivery
- [ ] Trial period management (14-day free trial)
- [ ] Payment failure handling and dunning
- [ ] Subscription upgrade/downgrade flows

**Success Criteria**:
- Customers can sign up and subscribe independently
- Payment processing reliable and secure
- Invoices automatically generated and sent

---

### 2.3 Onboarding & Customer Acquisition
**Priority**: High
**Timeline**: Week 20-22

- [ ] Self-service registration flow
- [ ] Interactive onboarding wizard
- [ ] Sample data and templates for quick start
- [ ] In-app help and documentation
- [ ] Customer support system (email-based ticketing)

**Success Criteria**:
- New customers can onboard without manual assistance
- Average onboarding time < 30 minutes
- Support requests trackable and resolved

---

### 2.4 Advanced Features
**Priority**: Medium
**Timeline**: Week 23-24

- [ ] Advanced reporting and analytics
- [ ] Export API for payroll system integrations
- [ ] Bulk import of employees (CSV)
- [ ] Custom approval workflow builder
- [ ] Email template customization per tenant

**Success Criteria**:
- Customers can customize workflows to their needs
- Integration API documented and tested
- Bulk operations functional for large employee lists

---

### Phase 2 Testing & Launch
**Timeline**: Week 25-26

- [ ] Beta testing with 5-10 pilot customers
- [ ] Marketing website and pricing page
- [ ] Payment processing tested end-to-end
- [ ] Public launch announcement
- [ ] Customer success program initiation

**Success Criteria**:
- 10 paying customers within first month
- No critical bugs in multi-tenant deployment
- Customer satisfaction score > 4/5

---

## Phase 3: Scale & Expansion (Months 7-12)
**Goal**: Grow customer base, add integrations, expand to new markets

### 3.1 Integration Ecosystem
**Priority**: High
**Timeline**: Month 7-8

- [ ] Primavera payroll system integration
- [ ] Sage PHC integration
- [ ] Centralgest integration
- [ ] API documentation for third-party integrations
- [ ] Webhook support for real-time data sync

**Success Criteria**:
- At least 2 major Portuguese payroll systems integrated
- API adoption by at least 20% of customers
- Integration setup time < 1 hour

---

### 3.2 Mobile Applications
**Priority**: Medium
**Timeline**: Month 9-10

- [ ] iOS native app (Swift/SwiftUI)
- [ ] Android native app (Kotlin)
- [ ] Push notifications for approvals
- [ ] Offline mode for request submission
- [ ] Biometric authentication

**Success Criteria**:
- Mobile apps available in App Store and Play Store
- Mobile adoption rate > 50% of users
- Mobile app rating > 4.5/5

---

### 3.3 Advanced Analytics & Forecasting
**Priority**: Medium
**Timeline**: Month 11

- [ ] Labor cost forecasting based on historical data
- [ ] Overtime trend analysis and alerts
- [ ] Time-off pattern insights
- [ ] Compliance risk dashboard
- [ ] Executive summary reports

**Success Criteria**:
- Managers can forecast labor costs 3 months ahead
- Analytics actionable and accurate
- Executive reports generated automatically

---

### 3.4 Geographic Expansion
**Priority**: High
**Timeline**: Month 12

- [ ] Research labor laws in 2 target countries (Spain, Italy, or France)
- [ ] Localization (language, currency, date formats)
- [ ] Country-specific overtime and time-off rules
- [ ] Regional holiday calendars
- [ ] Legal compliance verification

**Success Criteria**:
- Platform available in 2 additional countries
- Compliance verified by local legal experts
- At least 5 customers in new markets

---

## Future Considerations (12+ Months)

### Advanced Features
- AI-powered approval recommendations
- Automated compliance auditing
- Employee engagement surveys
- Performance review integration
- Advanced scheduling and shift management

### Enterprise Features
- SSO (Single Sign-On) integration
- Advanced security (2FA, IP whitelisting)
- Custom SLAs and dedicated support
- White-label options
- On-premise deployment options

### Integration Expansion
- Google Calendar / Outlook Calendar sync
- Slack/Teams notifications
- Accounting software integrations (Xero, QuickBooks)
- HRIS integrations (BambooHR, Personio)

---

## Success Metrics by Phase

### Phase 1 (Month 3)
- ✅ 1 production customer actively using platform
- ✅ 95%+ accuracy in overtime calculations
- ✅ 60% reduction in administrative time for customer
- ✅ Zero critical bugs after 2 weeks in production

### Phase 2 (Month 6)
- ✅ 10 paying customers
- ✅ SaaS platform live and accepting subscriptions
- ✅ MRR (Monthly Recurring Revenue) established
- ✅ Customer satisfaction score > 4/5

### Phase 3 (Month 12)
- ✅ 50+ active customers
- ✅ Expansion to 2 additional countries
- ✅ At least 2 payroll system integrations live
- ✅ Net Promoter Score (NPS) > 50
- ✅ Mobile apps launched

---

## Risk Mitigation

### Technical Risks
- **Database performance**: Implement caching (Redis), optimize queries, plan for read replicas
- **Multi-tenancy security**: Regular security audits, penetration testing, data isolation verification
- **Integration complexity**: Start with API exports, then build direct integrations incrementally

### Business Risks
- **Customer acquisition**: Leverage existing customer as case study, focus on referrals initially
- **Competition**: Emphasize compliance-first approach and Portuguese market specialization
- **Market timing**: Launch MVP quickly to validate product-market fit before full SaaS build

### Regulatory Risks
- **Labor law changes**: Monitor Portuguese labor law updates, plan for quarterly compliance reviews
- **Data privacy (GDPR)**: Ensure GDPR compliance from day one, regular privacy audits

---

## Prioritization Rationale

**Phase 1** focuses on delivering core value to the existing customer. This validates product-market fit and generates real-world usage data before scaling.

**Phase 2** transforms the validated product into a scalable SaaS platform. Multi-tenancy and billing infrastructure are essential before customer acquisition.

**Phase 3** drives growth through integrations, mobile apps, and geographic expansion. These features differentiate PeopleDear in the market and increase customer lifetime value.

---

**Last Updated**: October 15, 2025