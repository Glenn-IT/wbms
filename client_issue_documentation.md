# Client Issue Management System

This system has been created with the following features:

## Database Table

- Created `client_issue_list` table with the required fields
- Includes foreign key relationship with `client_list` table

## Features Implemented:

### Data Grid View

- Displays Meter ID, Name, Issue Status (Pending/Resolved), Date Created, and Actions
- Status badges with color coding (Yellow for Pending, Green for Resolved)
- Dropdown action menu for each row

### Actions Available:

1. **View** - Opens modal showing:

   - Meter ID
   - Client Name
   - Zone (Address)
   - Contact
   - Issue Title
   - Remarks
   - Image (if uploaded)
   - Date Created/Updated
   - Print and Back buttons

2. **Edit** - Opens modal for editing:

   - Remarks field (editable)
   - Image upload functionality
   - Option to remove existing image

3. **Delete** - Removes the issue record completely

4. **Resolve/Pending** - Toggle between status states

### Additional Features:

- File upload system for issue images stored in `/uploads/issues/`
- Print functionality for issue details
- DataTables integration for sorting, searching, and pagination
- Bootstrap modals for clean UI
- Image management (upload, replace, remove)
- Success/error notifications using SweetAlert2

## Files Created/Modified:

1. `/database/client_issue_table.sql` - Database structure
2. `/database/sample_client_issues.sql` - Sample data
3. `/admin/client_issue/index.php` - Main listing page
4. `/admin/client_issue/view_issue.php` - View modal
5. `/admin/client_issue/manage_issue.php` - Create/Edit form
6. `/classes/Master.php` - Added backend functions
7. `/admin/inc/navigation.php` - Updated menu link
8. `/uploads/issues/` - Directory for issue images

## Usage:

1. Navigate to "Client Issues" from the admin menu
2. Click "Create New Issue" to add new issues
3. Use the Action dropdown for View, Edit, Delete, or status changes
4. Upload images when creating or editing issues
5. Print issue details from the view modal

The system is fully functional and ready to use!
