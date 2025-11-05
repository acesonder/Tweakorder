# Admin Reports Documentation

## Overview
The Admin Reports system provides powerful analytics and reporting tools for administrators and managers. It includes advanced data visualization, multiple export formats, and a modern 3D-styled interface.

## Features

### Report Types

#### 1. Sales Report
- **Daily Order Trends**: Line chart showing order volume over time
- **Order Status Distribution**: Pie chart of order statuses
- **Top Products**: Most frequently ordered items
- **Summary Statistics**: Total orders, fulfillment rate, unique clients
- **Daily Breakdown**: Detailed statistics for each day in the range

#### 2. Inventory Report
- **Stock Distribution**: Current inventory status (in stock, low stock, out of stock)
- **Product Popularity**: Most ordered products in the last 30 days
- **Low Stock Alerts**: Products requiring restocking
- **Detailed Product List**: All products with inventory and order statistics

#### 3. Client Activity Report
- **Client Engagement Metrics**: Order frequency and patterns
- **Top Active Clients**: Clients with most orders
- **New Client Tracking**: Recently registered clients
- **Client History**: Complete order history per client

#### 4. Staff Performance Report
- **Activity Tracking**: Staff actions and productivity
- **Order Management Stats**: Orders created and updated by staff
- **Staff Comparison**: Relative performance metrics
- **Role-based Analysis**: Activity by staff role

#### 5. Comprehensive Report
- **Tabbed Interface**: All report types in one view
- **Unified Analysis**: Complete system overview
- **Cross-Reference Data**: Correlations between different metrics

### Export Capabilities

#### PDF Export
- High-quality PDF generation using jsPDF and html2canvas
- Includes all charts and tables
- Multi-page support for long reports
- Print-ready format

#### CSV Export
- Structured data in CSV format
- Compatible with Excel and other spreadsheet applications
- Includes headers and formatted data
- Separate sections for different data types

#### Excel Export
- Currently exports as CSV (extensible for full Excel features)
- Suggestion: Integrate SheetJS for native Excel format

### Date Filtering

#### Quick Presets
- **Today**: Current day's data
- **This Week**: Last 7 days
- **This Month**: Current month from first day
- **This Quarter**: Current quarter
- **This Year**: Year-to-date

#### Custom Range
- Select any start and end date
- Flexible analysis periods
- Historical data access

### Visual Design

#### 3D Effects
- CSS transforms with perspective
- Depth and elevation on cards
- Smooth hover animations
- Modern, professional appearance

#### Responsive Design
- Mobile-friendly interface
- Tablet optimization
- Desktop full-screen layouts
- Adaptive grid system

#### Modal Viewer
- Full-screen report viewing
- Distraction-free analysis
- Easy close and navigation
- Animated transitions

## Technical Implementation

### Backend API (`api/reports.php`)

#### Endpoints
- `get_sales_report`: Sales analytics data
- `get_inventory_report`: Inventory status and alerts
- `get_client_activity_report`: Client engagement metrics
- `get_staff_performance_report`: Staff productivity data
- `get_comprehensive_report`: Combined report data
- `export_report_data`: Data export in various formats

#### Security Features
- **Authentication**: Session-based verification
- **Authorization**: Admin and Manager roles only
- **SQL Injection Prevention**: Prepared statements with parameter binding
- **Error Handling**: Graceful error messages without exposing internals

#### Database Queries
All queries use prepared statements:
```php
$stmt = $conn->prepare("SELECT ... WHERE created_at BETWEEN ? AND ?");
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
```

### Frontend (`admin-reports.html`)

#### Libraries Used
- **Chart.js 4.4.0**: Interactive charts and graphs
- **jsPDF 2.5.1**: PDF generation
- **html2canvas 1.4.1**: HTML to canvas conversion for PDF

#### Chart Types
- **Line Charts**: Trend analysis over time
- **Bar Charts**: Comparisons and rankings
- **Pie Charts**: Distribution and proportions
- **Doughnut Charts**: Status breakdowns

#### JavaScript Functions
- `generateReport()`: Fetch and render report data
- `exportToCSV()`: Convert data to CSV format
- `exportToExcel()`: Excel export wrapper
- `exportToPDF()`: Generate PDF from HTML
- `openReportModal()`: Display report in modal
- Chart creation functions for each report type

## Usage Guide

### Accessing Reports
1. Log in with Admin or Manager credentials
2. Navigate to Admin Panel
3. Click "📊 Reports" in the header
4. Select report type and date range
5. Click "Generate Report"

### Exporting Data
1. Generate the desired report
2. Choose export format:
   - **CSV**: For data analysis
   - **Excel**: For spreadsheet work
   - **PDF**: For presentations and printing
3. Click the corresponding export button
4. File downloads automatically

### Viewing in Modal
1. Generate a report
2. Click "View in Modal" button
3. Full-screen view opens
4. Click X or outside modal to close

### Customizing Date Ranges
1. Use Quick Select dropdown for common periods
2. Or manually select Start Date and End Date
3. Click "Generate Report" to update

## Performance Considerations

### Current Implementation
- Uses existing database schema
- FIND_IN_SET for product matching (temporary solution)
- Real-time report generation
- Client-side chart rendering

### Optimization Recommendations

#### Database Structure
Consider implementing an `order_items` junction table:
```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);
```

Benefits:
- Faster queries with proper indexing
- More reliable product matching
- Better data integrity
- Support for quantities per product

#### Caching Strategy
For high-traffic deployments:
- Cache report data for common date ranges
- Use Redis or Memcached
- Invalidate cache on data changes
- Reduce database load

#### Report Scheduling
For large datasets:
- Generate reports asynchronously
- Email completed reports
- Store historical snapshots
- Background job processing

## Security Best Practices

### Implemented
✅ Prepared statements for all queries
✅ Role-based access control
✅ Session validation
✅ Input sanitization
✅ Error message sanitization

### Additional Recommendations
- Implement rate limiting for export functions
- Add audit logging for report access
- Encrypt sensitive data in transit
- Regular security audits
- Database connection pooling

## Troubleshooting

### Common Issues

#### Report Shows "Error loading report"
- Check database connection
- Verify user has Admin/Manager role
- Ensure date range is valid
- Check browser console for errors

#### Charts Not Displaying
- Verify Chart.js loaded (check network tab)
- Ensure data format is correct
- Check browser JavaScript console
- Try refreshing the page

#### PDF Export Fails
- Check browser console for errors
- Verify jsPDF and html2canvas loaded
- Try reducing report size
- Check browser compatibility

#### CSV Contains No Data
- Ensure report generated successfully
- Check data format in API response
- Verify browser download settings
- Check for JavaScript errors

## Browser Compatibility

### Supported Browsers
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Opera 76+

### Required Features
- ES6 JavaScript support
- CSS Grid and Flexbox
- HTML5 Canvas
- Async/Await

## Future Enhancements

### Planned Features
- [ ] Report scheduling and email delivery
- [ ] Custom report builder
- [ ] Dashboard widgets
- [ ] Data comparison (period over period)
- [ ] Advanced filtering (by product, client, staff)
- [ ] Saved report templates
- [ ] Multi-format batch export
- [ ] Real-time data refresh
- [ ] Report sharing and permissions
- [ ] Mobile app integration

### Integration Opportunities
- Google Analytics integration
- Third-party BI tools
- Data warehouse export
- API endpoints for external systems
- Webhook notifications

## Support and Maintenance

### Updating Report Queries
1. Edit `api/reports.php`
2. Modify the relevant function
3. Test with sample data
4. Update documentation

### Adding New Report Types
1. Create new function in `api/reports.php`
2. Add case in switch statement
3. Create render function in HTML
4. Add option to dropdown
5. Update documentation

### Customizing Charts
1. Locate chart creation function in HTML
2. Modify Chart.js configuration
3. Adjust colors, labels, options
4. Test responsiveness

## API Reference

### Request Format
```
GET /api/reports.php?action={action}&start_date={YYYY-MM-DD}&end_date={YYYY-MM-DD}
```

### Response Format
```json
{
  "success": true,
  "report_type": "sales",
  "date_range": {
    "start": "2025-11-01",
    "end": "2025-11-05"
  },
  "summary": {
    "total_orders": 150,
    "fulfilled_orders": 120,
    "pending_orders": 30,
    "unique_clients": 75
  },
  "data": [...],
  "generated_at": "2025-11-05 12:00:00"
}
```

### Error Response
```json
{
  "success": false,
  "error": "Unauthorized - Admin or Manager role required"
}
```

## License
Part of Tweakorder system - see main LICENSE file.

## Contributors
- Developed by GitHub Copilot
- Integrated into Tweakorder v2.0+
