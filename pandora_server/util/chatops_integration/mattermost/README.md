# Pandora FMS Mattermost Plugin
A plugin for [Pandora FMS](https://github.com/pandorafms/pandorafms) to enable notifications to Mattermost Open Source Chat.

# Usage
Assuming you are using Pandora FMS 6.0, the steps are:

1. Create the [Alert command](http://wiki.pandorafms.com/index.php?title=Pandora:Documentation_en:Alerts#The_Alert_Command_.28Pandora_Versions_5_and_above_only.29) going to Alerts -> Commands and clicking on "Create". Then:
![create command](help/images/1-set-up-the-mattermost-command.png?raw=true "Set up Mattermost Command")

2. Define the [Alert Action](http://wiki.pandorafms.com/index.php?title=Pandora:Documentation_en:Alerts#Alert_Actions_.28all_Pandora_FMS_versions_including_5.0.29) going to Alerts -> Actions and clicking on "Create". Then:
![create action](help/images/2-set-up-the-mattermost-action.png?raw=true "Set up Mattermost Action")

3. Assign the action to an existing module under Alerts -> List of alerts:
![assign template to module](../help/images/3-assign-template-to-module.png?raw=true "Assign a template to a module")

4. Optinionally, go to your agent and verify the alert has been created:
![Verify the alert creation](../help/images/4-verify.png?raw=true "Verify the alert creation")
	
When the alert triggers, the result would be something like this:
![Mattermost-real-example](../help/images/5-mattermost-result.png?raw=true "Mattermost real example")
