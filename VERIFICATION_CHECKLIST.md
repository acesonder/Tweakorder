# Verification Checklist - Product Images and Order Workflow Customization

## Pre-Deployment Verification

### Database Setup
- [ ] Run `order_workflow_customization.sql` on production database
- [ ] Verify all 7 tables created successfully:
  - [ ] order_workflow_templates
  - [ ] order_workflow_steps
  - [ ] user_workflow_preferences
  - [ ] user_product_display
  - [ ] order_quick_templates
  - [ ] client_order_preferences
  - [ ] order_custom_fields
- [ ] Confirm 3 default workflow templates exist
- [ ] Verify default preferences created for existing users
- [ ] Check all foreign key constraints are valid

### File Structure
- [ ] Verify `images/harmreduction/` directory exists
- [ ] Confirm 26 SVG files present in harmreduction directory
- [ ] Check `assets/uploads/` directory exists and is writable
- [ ] Verify all new HTML files are accessible:
  - [ ] edit-product.html
  - [ ] order-workflow-settings.html
  - [ ] create-order-custom.html

### API Endpoints
- [ ] Test `api/products.php` POST (with file upload)
- [ ] Test `api/products.php` POST (with local image path)
- [ ] Test `api/products.php` PUT (update with image)
- [ ] Test `api/order-workflow.php` GET (all actions)
- [ ] Test `api/order-workflow.php` POST (all actions)
- [ ] Test `api/order-workflow.php` DELETE (template deletion)

### Security Validation
- [ ] All SQL queries use prepared statements
- [ ] File upload type validation working
- [ ] Session validation on API endpoints
- [ ] No SQL injection vulnerabilities
- [ ] Path traversal protection for images
- [ ] XSS protection in place

### Functional Testing

#### Product Image Upload
- [ ] Can upload JPEG image
- [ ] Can upload PNG image
- [ ] Can upload SVG image
- [ ] Can reference local image path
- [ ] Image preview works on add page
- [ ] Image preview works on edit page
- [ ] Current image displays on edit page
- [ ] Invalid file types rejected
- [ ] Images display correctly in order forms

#### Edit Product Page
- [ ] Page loads with product ID parameter
- [ ] Product data populates correctly
- [ ] Can update product name
- [ ] Can update description
- [ ] Can update inventory
- [ ] Can toggle favorite status
- [ ] Can change background color
- [ ] Can update image (upload)
- [ ] Can update image (local path)
- [ ] Can keep existing image
- [ ] Save functionality works
- [ ] Redirects after successful save

#### Workflow Settings
- [ ] Page loads without errors
- [ ] Workflow templates display
- [ ] Can select a template
- [ ] Display mode dropdown works
- [ ] Sort order dropdown works
- [ ] Toggle switches function
- [ ] Default status dropdown works
- [ ] Save settings works
- [ ] Reset to defaults works
- [ ] Settings persist after save

#### Custom Order Form
- [ ] Page loads with user preferences
- [ ] Product display respects selected mode
- [ ] Product sorting works correctly
- [ ] Search filter works
- [ ] Category filter works
- [ ] Favorites toggle works
- [ ] Can select products (quantity increases)
- [ ] Can navigate between steps
- [ ] Client search works
- [ ] Can add new client
- [ ] Final summary displays correctly
- [ ] Order creation works
- [ ] Quick order mode works (if enabled)

#### Navigation Integration
- [ ] Staff dashboard links work
- [ ] Console editor button works
- [ ] Workflow settings accessible
- [ ] All navigation links valid

### Performance Testing
- [ ] Page load times acceptable (<2 seconds)
- [ ] Product list loads quickly (with 100+ products)
- [ ] Image uploads complete reasonably (<5 seconds)
- [ ] API responses fast (<1 second)
- [ ] No memory leaks in JavaScript
- [ ] Database queries optimized

### Browser Compatibility
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari
- [ ] Mobile browsers (responsive design)

### Accessibility
- [ ] Forms have proper labels
- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] Color contrast sufficient
- [ ] Error messages clear

### Documentation
- [ ] ORDER_WORKFLOW_CUSTOMIZATION_GUIDE.md complete
- [ ] IMPLEMENTATION_SUMMARY_IMAGES_AND_WORKFLOW.md accurate
- [ ] Code comments adequate
- [ ] API documentation clear

## Post-Deployment Monitoring

### First 24 Hours
- [ ] Monitor error logs for PHP errors
- [ ] Check database for unexpected entries
- [ ] Verify file upload directory size
- [ ] Confirm no performance degradation
- [ ] Check user feedback

### First Week
- [ ] Review workflow template usage
- [ ] Check quick template creation/usage
- [ ] Monitor image upload success rate
- [ ] Verify preference persistence
- [ ] Collect user feedback

### Ongoing
- [ ] Regular backup of workflow preferences
- [ ] Monitor disk usage (uploaded images)
- [ ] Track popular workflow templates
- [ ] Review and optimize queries
- [ ] Update documentation as needed

## Issue Resolution

### Common Issues

**Images Not Displaying**
- Verify image path is correct
- Check file permissions on images directory
- Confirm web server can access the path
- Validate image file format

**Preferences Not Saving**
- Check database connection
- Verify user session valid
- Check for JavaScript console errors
- Confirm API endpoint responding

**Workflow Not Loading**
- Verify database tables exist
- Check template_id in preferences
- Confirm workflow steps configured
- Review browser console for errors

**Upload Failing**
- Check file size limits (php.ini)
- Verify upload directory writable
- Confirm file type in allowed list
- Check disk space available

## Rollback Plan

If critical issues arise:

1. **Database Rollback**
   ```sql
   DROP TABLE IF EXISTS order_custom_fields;
   DROP TABLE IF EXISTS client_order_preferences;
   DROP TABLE IF EXISTS order_quick_templates;
   DROP TABLE IF EXISTS user_product_display;
   DROP TABLE IF EXISTS user_workflow_preferences;
   DROP TABLE IF EXISTS order_workflow_steps;
   DROP TABLE IF EXISTS order_workflow_templates;
   ```

2. **File Rollback**
   - Remove new HTML files if causing issues
   - Revert api/products.php if needed
   - Update navigation links back to original

3. **Cache Clear**
   - Clear browser caches
   - Clear server-side caches if applicable
   - Restart PHP-FPM if needed

## Success Criteria

Implementation is successful when:
- ✅ All database tables created without errors
- ✅ All new pages load without errors
- ✅ Product images can be uploaded and displayed
- ✅ Local image paths work correctly
- ✅ Workflow settings can be saved and applied
- ✅ Custom order form respects preferences
- ✅ No security vulnerabilities introduced
- ✅ No performance degradation
- ✅ All existing functionality still works
- ✅ Documentation is complete and accurate

## Sign-Off

- [ ] Developer tested all features
- [ ] Code review completed
- [ ] Security review passed
- [ ] Documentation reviewed
- [ ] QA testing completed (if applicable)
- [ ] Stakeholder approval received
- [ ] Production deployment approved

**Developer:** ___________________ **Date:** ___________

**Reviewer:** ___________________ **Date:** ___________

**Approved By:** ___________________ **Date:** ___________

## Notes

Additional notes or concerns:
_______________________________________________
_______________________________________________
_______________________________________________
