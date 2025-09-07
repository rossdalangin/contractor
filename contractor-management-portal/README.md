# Contractor Management Portal

A custom WordPress plugin to manage contractors, clients, channels, tasks, invoicing, and reporting.

## Description

This plugin provides a comprehensive portal for managing a contractor-based business within the WordPress admin area. It allows administrators to manage clients, channels, tasks, and invoices, and to assign tasks to contractors. Contractors have their own dashboard where they can view their assigned tasks and submit invoices.

### Key Features
*   **Role-Based Access:** Custom user roles for 'Administrator', 'Contractor', and 'Client'.
*   **Admin Dashboard:** A central dashboard for managing all aspects of the business.
*   **Custom Post Types:** Separate management screens for Clients, Channels, Tasks, and Invoices.
*   **Task Management:** Admins can create tasks and assign them to contractors.
*   **Contractor Dashboard:** A front-end dashboard for contractors to view their assigned tasks.
*   **Invoicing:** Contractors can submit invoices for completed tasks. Admins can review and approve them.

## Installation

1.  Download the plugin as a `.zip` file.
2.  In your WordPress admin area, go to **Plugins > Add New**.
3.  Click on **Upload Plugin**.
4.  Choose the downloaded `.zip` file and click **Install Now**.
5.  After the installation is complete, click **Activate Plugin**.

Upon activation, the plugin will create:
*   The necessary custom user roles ('Contractor', 'Client').
*   A 'Contractor Dashboard' page on the front-end.
*   The default statuses for invoices ('Pending', 'Approved', 'Paid', 'Rejected').

## Process Guide

This guide explains the typical workflow for administrators and contractors using the plugin.

### For Administrators

1.  **Adding Contractors:**
    *   Go to **Users > Add New**.
    *   Fill in the user details (username, email, etc.).
    *   From the **Role** dropdown, select **Contractor**.
    *   Click **Add New User**. The new user will now be a contractor.

2.  **Managing CPTs (Clients, Channels, Tasks, Invoices):**
    *   In the WordPress admin menu, go to **Contractor Mgmt**.
    *   Here you will find sub-menus for **Clients**, **Channels**, **Tasks**, and **Invoices**.
    *   You can add, edit, and delete items in each of these sections as with any other post type in WordPress.

3.  **Assigning Tasks to Contractors:**
    *   Go to **Contractor Mgmt > Tasks** and click **Add New** or edit an existing task.
    *   On the task edit screen, you will find a meta box titled **Assign Contractor**.
    *   Select a contractor from the dropdown list.
    *   Save or update the task.

4.  **Reviewing Invoices:**
    *   Go to **Contractor Mgmt > Invoices**.
    *   Here you will see a list of all submitted invoices.
    *   You can edit an invoice to view its details.
    *   To approve or change the status of an invoice, use the **Invoice Status** meta box on the invoice edit screen.

### For Contractors

1.  **Logging In:**
    *   Contractors log in to the WordPress site using the standard login form.

2.  **Viewing Assigned Tasks:**
    *   After logging in, contractors should navigate to the **Contractor Dashboard** page. The URL for this page is typically `your-site.com/contractor-dashboard`.
    *   This page will display a list of all tasks that have been assigned to them.
    *   Clicking on a task will take them to the single task page with more details.

3.  **Submitting Invoices:**
    *   On the single task page, if an invoice has not yet been submitted for that task, the contractor will see a **Submit Invoice** button.
    *   Clicking this button will automatically create a new invoice with the status 'Pending'.
    *   If an invoice has already been submitted, the contractor will see the current status of the invoice instead of the button.
