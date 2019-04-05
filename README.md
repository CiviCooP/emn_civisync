# EMN CiviSync 

Syncs membership organisations from European microfinance CiviCRM system to the website.

## What does it do?

* Calls an CiviCRM api that selects all the members.
* Members are identified on the website.
* The details of the members are updated so that they are the same as in the CRM.
* The logo's from the contact are also copied to the website.

Identification is done as follows.
* In CiviCRM each member has a contact_id. This is stored in the site. First the sync tries to find an organisation with the same contact_id. If it is not found it searches on the name.

When an organisations is not found it can be created (with the risque of duplicates). Or the sync only signals the missing organisations.

## How is it used?

In the Content section a menu entry 'CiviCRM Sync' is added. This leads to form that can be used to start the sync. The screen offers the choice what to do with organisations that are not found.

## Preparations

The following preparations must be done before the sync can be used.
* add a directory `civicrmlogos` to the `files` directory. This is the place where the logos are stored.
* add a field `CiviCRM ID` to the contact type organisation. This field must have the machine name `field_contact_id` and the type Number (Integer).
* configure the correct parameters for the api call to CiviCRM. In the `Coniguration - System` menu a link to a form is added where these parameters can be enterd.


