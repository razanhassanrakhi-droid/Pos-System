# Customer Management Redesign (2026 Modern UX/UI)

This document outlines the plan to redesign the Customer Management module to match a modern SaaS-style user experience. The update transforms the simple data entry form into a professional customer profile management system, improving user experience, visual hierarchy, and paving the way for future CRM expansions.

## User Review Required

> [!IMPORTANT]
> - **Database Schema Updates**: We will add several new columns to the `customers` table (`customer_type`, `status`, `dob`, `loyalty_points`, `credit_limit`, `balance`) to support the new features and future-proof the system.
> - **Customer Code Format**: We will ensure the `customer_number` generation follows the `CUS-20260621-00001` format as requested (re-enabling date prefix for customers).
> - **UI Transformation**: The traditional table and modals will be completely replaced with a modern, structured directory design and multi-section forms.

## Open Questions

> [!WARNING]
> - **Backwards Compatibility**: Currently, the Edit Customer modal fetches data via the `CustomerController@show` method. Should we keep `show` returning JSON for the modal and create a new `profile` method for the new Profile Page, or should we change `show` to return the HTML view and create a separate API route (e.g., `api/customers/{id}`) for the modal data? *Proposed solution: Keep `show` for JSON if requested via AJAX, otherwise return the profile view.*
> - **Customer Code**: Do you want to retroactively update existing customer codes to match the new format, or only apply this format to new customers?

## Proposed Changes

---

### Database & Models

#### [NEW] `database/migrations/xxxx_xx_xx_xxxxxx_add_crm_fields_to_customers_table.php`
- Add `customer_type` (enum/string: 'Walk-in', 'Regular', 'Wholesale', 'VIP') default 'Regular'.
- Add `status` (enum/string: 'Active', 'Inactive', 'Blocked') default 'Active'.
- Add `dob` (date, nullable).
- Add `loyalty_points` (integer, default 0).
- Add `credit_limit` (decimal 15,2 default 0).
- Add `balance` (decimal 15,2 default 0).

#### [MODIFY] `app/Models/Customer.php`
- Add the new columns to the `$fillable` array.
- Update the `customer_number` generation logic to include the date prefix `CUS-YYYYMMDD-XXXXX`.
- Add relationships for `sales()` and `returns()`.

---

### Controllers

#### [MODIFY] `app/Http/Controllers/CustomerController.php`
- `index()`: Implement filtering logic for Customer Type and Status. Provide necessary stats (Customer Count) to the view.
- `store()` & `update()`: Add validation and saving logic for the new fields (`customer_type`, `status`, `dob`).
- `show()`: Update to return the new `customers.profile` view if the request is a standard web request, and return JSON if it's an AJAX request (for the edit modal). Calculate statistics (Total Orders, Total Purchases, Avg Order, Last Purchase Date) and load Purchase/Return history for the profile view.

---

### Views

#### [MODIFY] `resources/views/customers/index.blade.php`
- Completely redesign the header to show "Customers", "Total Count", and the Add button.
- Move search and filters (Type, Status) to the top bar.
- Redesign the table to include Customer Code, Name, Phone, Customer Type (with badges), Status (with badges), Total Orders, Total Purchases, Last Purchase, and Actions.
- Redesign the "Add Customer" modal to use a multi-section layout:
    - **Section 1**: Basic Info (Name, Phone, Email, Type, Status).
    - **Section 2**: Additional Info (Address, Date of Birth, Notes).
    - **Section 3**: System Info (Read-only Code, Created Date).
- Implement responsive card-based layout for mobile screens.

#### [NEW] `resources/views/customers/profile.blade.php`
- Create a modern profile dashboard for individual customers.
- **Header Card**: Customer Name, Badges, Phone, Email, Code.
- **Statistics Section**: KPI cards for Total Orders, Total Purchases, Average Order, Last Purchase Date.
- **Purchase History Section**: Table showing past invoices, dates, amounts, status, and view/print actions.
- **Returns History Section**: Table showing past returns associated with the customer.

#### [MODIFY] `resources/lang/ar/pos.php` & `resources/lang/en/pos.php`
- Add all necessary translations for the new fields, badges, statuses, and profile page headers.

## Verification Plan

### Automated Tests
- Run database migrations to ensure columns are added cleanly.
- Verify `Customer` model creation with the new code generation format.

### Manual Verification
- Navigate to the Customer list and confirm the modern SaaS design is applied.
- Test filtering by Type and Status.
- Add a new customer using the multi-section modal and verify the data saves correctly (including Type and Status).
- Click on a customer to view their new Profile Page.
- Verify that the statistics (Total Orders, Total Purchases) match their actual history.
- Ensure the layout is responsive and stacks correctly on mobile screens.
