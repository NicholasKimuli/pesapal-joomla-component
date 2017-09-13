README

Contents
1. Installation
2. Set up
3. Functionality
4. Approach

1. Installation

1.1 Open the administrator end of your Joomla! Installation
1.2 On the Extensions menu, click on Manage then click on install
1.3 Using the upload tool provided, upload the com_donation.zip file which will install the component extension

2. Set up

2.1 To set up, click on the Components menu at the administrator end of your installation then click on the Donations - Pesapal component. This appears after you install the component. 
2.2 From the view that is displayed, click on Options at the top right corner
2.3 Enter your Pesapal Consumer Key and Consumer Secret. To find out more about these, go to this link https://demo.pesapal.com

2.4 Set up Menu Item
2.4.1 From the administrator dashboard, click on the Menus menu item and then click on All Menu Items.
2.4.2 Click on New then set up your menu details. On the Menu Item Type field, click on it and select Pesapal Donation and select the Pesapal Donation item with the description 'Accept donations using Pesapal'
2.4.3 Assign your menu item to a menu then click on Save.

2.5 You are now ready to start accepting donations through your website powered by Pesapal. Click on the menu item you created from the frontend.

3. Functionality

3.1 Collection of donation details i.e Donor name, email, phone number, donation amount, donation description and the Time Period
3.2 Payment through Pesapal using the details collected above
3.3 Email notification to donor (uses the email submitted via the form) with link they can use to complete the payment
3.4 Administrator dashboard with all the donations made and their statuses

4. Approach

4.1 Donation Site View

This is where the form that collects the donation details is displayed, where the iFrame is displayed on form submission and where a donor is able to complete a payment after they click on the link in the email.

When the form is submitted, after all the required information is submitted, a new donation record is created in the database. An email is also sent using the Elastic Mail API - https://elasticemail.com (I used this to make sure emails are delivered no matter the server Joomla! is installed. Works as long as CURL is enabled). 

To generate the iframe, the OAuth PHP class provided on the Pesapal Samples & Downloads section of the Developer site is used. It is also used on Callback.

When a user clicks on the link they receive via email, the donations model looks for the reference code from the url. If it is found, the donation with that code is fetched then it's details are used to generate the iframe. If not, the default form is displayed.

4.2 Callback Site View

When a payment is completed, a user is directed to this view. The payment status is checked on this view and the database is updated accordingly. 

4.3 Administrator view

Data from the database is pulled via the model and is displayed using the template provided. 

5. Conclusion

I used the MVC approach touted by Joomla! but not in all cases. There are some cases where queries were made on the view as opposed to the model. 

