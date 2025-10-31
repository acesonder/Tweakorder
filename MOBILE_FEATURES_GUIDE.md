# Mobile Order System & Case Management - New Features Guide

## Overview
This update introduces a comprehensive mobile-first order system, case management features, and error logging for the Tweakorder application. These features are specifically designed for outreach workers helping people experiencing addiction, homelessness, and mental health issues.

## New Features

### 1. Mobile Order System (mobile-order.html)

**Key Features:**
- **Single-Page Flow**: Streamlined order creation on one page
- **Client Dropdown with Quick Add**: Select existing clients or add new ones with a "+" button
- **Modal Client Creation**: Create new clients with contact information
- **Product Selection**: Alphabetically sorted product grid with custom gradients
- **Quantity Badges**: Visual counters showing selected quantities
- **Order Confirmation**: Review page with tap-to-remove and swipe-to-delete
- **Success Overlays**: Visual feedback for successful actions
- **Error Logging**: Automatic error reporting to admin dashboard

**Navigation Flow:**
1. Select client from dropdown or create new client with "+"
2. Tap products to add to order (shows quantity badge)
3. Click "Next" to review order
4. Tap items to remove one, swipe right-to-left to remove all
5. Click "Confirm Order" to submit

### 2. Alternative Mobile UI Styles

Five different mobile interfaces optimized for different use cases:

#### a) Card-Based Layout (mobile-style-card.html)
- Vibrant gradient cards
- Quick-add buttons
- Visual cart summary
- Best for: Visual learners, quick interactions

#### b) List View (mobile-style-list.html)
- Compact list with inline controls
- +/- quantity buttons
- Sticky header and footer
- Best for: Efficiency, seeing all products at once

#### c) Grid with Categories (mobile-style-grid.html)
- Dark theme
- Category filtering (All, Supplies, Hygiene, Clothing)
- 3-column grid layout
- Best for: Organized product browsing

#### d) Compact Single-Column (mobile-style-compact.html)
- Minimal design
- Floating action button (FAB)
- Single-column list
- Best for: Small screens, one-handed use

#### e) Tabbed Interface (mobile-style-tabbed.html)
- Three-step process (Client → Items → Review)
- Tab navigation
- Clear progress indication
- Best for: Structured workflows, new users

### 3. Case Management System

**Features:**
- **50 Pre-built Templates** covering:
  - 15 Addiction support templates
  - 15 Homelessness service templates
  - 20 Mental health templates
- **Case Notes**: Document client interactions
- **Template Categories**: Addiction, Homelessness, Mental Health, General, Referral
- **Client Filtering**: View notes by client or category
- **Follow-up Tracking**: Set follow-up dates
- **Confidential Notes**: Mark sensitive information

**Template Examples:**
- Initial Substance Use Assessment
- Harm Reduction Plan
- Housing Needs Assessment
- Emergency Shelter Placement
- Mental Health Screening
- Crisis Intervention
- PTSD Trauma-Informed Care
- And 43 more...

**Access:**
Navigate to "Case Management" from the main dashboard

### 4. Error Logging System

**Admin Dashboard:**
- Real-time error monitoring
- Severity levels: Critical, High, Medium, Low
- Error statistics dashboard
- Filter by status (Unresolved/Resolved) and severity
- Mark errors as resolved
- Auto-refresh every 30 seconds

**Error Types Logged:**
- Client creation errors
- Order submission errors
- API failures
- System errors

**Access:**
Navigate to "Error Logs" from the main dashboard

## Database Updates

### New Tables:

1. **error_logs**
   - Tracks system errors with severity levels
   - Links to users and clients
   - Resolution tracking

2. **case_templates**
   - 50 pre-built templates
   - Categorized by issue type
   - Reusable content

3. **case_notes**
   - Client case documentation
   - Template-based or custom
   - Follow-up date tracking
   - Confidentiality flags

4. **worker_preferences**
   - User UI style preferences
   - Theme settings
   - Custom preferences JSON

### Updated Tables:

**clients** - Added contact fields:
- phone
- email
- address
- emergency_contact

## Installation

### 1. Update Database Schema

```bash
mysql -u root -p tweakorder < database.sql
```

### 2. Load Case Templates

```bash
mysql -u root -p tweakorder < case_templates_data.sql
```

### 3. Verify Database Connection

The system now supports both MySQLi (legacy) and PDO (new features).

## API Endpoints

### Case Management
- **GET/POST** `/api/case-notes.php` - Manage case notes
- **GET/POST** `/api/case-templates.php` - Manage templates

### Error Logging
- **GET/POST/PUT** `/api/error-logs.php` - Error log management

### Clients (Updated)
- **POST** `/api/clients.php` - Now accepts phone, email, address fields

## Usage Examples

### Creating a Case Note

```javascript
const formData = new URLSearchParams();
formData.append('client_id', 123);
formData.append('category', 'addiction');
formData.append('template_id', 1); // Optional
formData.append('note_content', 'Client presented with...');
formData.append('follow_up_date', '2025-11-15');
formData.append('is_confidential', 0);

fetch('api/case-notes.php', {
    method: 'POST',
    body: formData
});
```

### Logging an Error

```javascript
const formData = new URLSearchParams();
formData.append('error_type', 'order_creation_error');
formData.append('error_message', 'Failed to create order');
formData.append('severity', 'high');
formData.append('page_url', window.location.href);

fetch('api/error-logs.php', {
    method: 'POST',
    body: formData
});
```

## Mobile Optimization

All new pages are mobile-first and include:
- Touch-optimized controls
- Swipe gestures (where applicable)
- Responsive layouts
- Minimal data usage
- Offline-friendly designs

## Security Features

- SQL injection prevention with prepared statements
- Input validation on all forms
- XSS protection
- Error logging without exposing sensitive data
- Confidential case notes support

## Best Practices

### For Outreach Workers:

1. **Choose Your Style**: Try different mobile styles to find what works best for your workflow
2. **Use Templates**: Start with templates for faster case note creation
3. **Set Follow-ups**: Always set follow-up dates for client continuity
4. **Mark Confidential**: Use confidential flag for sensitive information
5. **Check Error Logs**: Report any persistent issues to admins

### For Administrators:

1. **Monitor Errors**: Check error logs regularly
2. **Review Case Notes**: Ensure proper documentation
3. **Customize Templates**: Add templates specific to your organization
4. **Train Staff**: Familiarize workers with different UI styles

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running
- Check config/database.php credentials
- Ensure database exists and tables are created

### Mobile Order Not Working
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check error logs for detailed information

### Case Templates Not Loading
- Ensure case_templates_data.sql was imported
- Check database permissions
- Verify API endpoint returns data

## Future Enhancements

Potential additions:
- Worker preference saving (UI style selection)
- Case note search and filtering
- PDF export for case notes
- Bulk client import
- Advanced analytics dashboard
- Push notifications for errors
- Offline mode with sync

## Support

For issues or questions:
1. Check error logs in admin dashboard
2. Review case note templates for examples
3. Test different mobile styles for your use case
4. Report bugs via GitHub issues

## License

Same as main Tweakorder project (MIT License)
