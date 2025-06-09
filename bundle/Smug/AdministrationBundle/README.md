<h1 align="center">Nodaro - Administration Bundle</h1>

## 🧭 Administration Bundle
The Administration Bundle is the central module for managing and controlling Norado through a dedicated backend. It provides a modular and extensible foundation for administrative features that can be used consistently across the entire system.

## 🔑 Core Features
 - User & Role Management: Manage users, access permissions, and roles.
 - Module-Based Navigation: Dynamically configurable page structure for integrating additional bundles or modules.
 - Configuration Management: Maintain global system settings (e.g. email, API keys, branding).

## ⚙️ Technical Specifications
 - Compatible with Symfony 6
 - UI built with Twig & Vue 3, styled using Tailwind CSS
 - REST interfaces for asynchronous interactions
 - Role-Based Access Control
 - Flexible extensibility through events and dependency injection

## Available webpack alias

You can use the assets from this Bundle by using the alias `@SmugAdministraionBundle`

## Overview of available form fields

| Element | Description |
| -------- | -------- |
| Accordion   | A basic accordion element to display children of the current value   |
| AvancedAssociation   | Provides a list of child elements with modification functionalities   |
| ApiSelection   | A Selectbox field to select an api endpoint    |
| Avatar   | A simple avatar field to display the user profile image    |
| Button   | A simple button for api calls     |
| Card   | A card element to display child entities in a list     |
| Checkbox   | A simple checkbox element for form data     |
| Colorpicker   | A simple colorpicker element     |
| Column   | Displays items inside a table column     |
| Content   | A Content editor for frontend sites or blog entries      |
| Datepicker   | A simple colorpicker element uses external library [flatPickr](https://flatpickr.js.org/)     |
| Editor   | Used for styleable textblocks. Uses external library [Quill Editor](https://vueup.github.io/vue-quill/)     |
| Email   | A simple e-mail element     |
| FileUpload   | A component which provides the ability to upload files or select a file from media library     |
| ImageGallery   | A simple gallery element to show multiple images of the item     |
| Infobox   | Display an infobox for different purposes     |
| ItemSelect   | A multistep selection component     |
| JsonOutputArray   | This component prints a prettified nested JSON     |
| MediaCenter   | A component for browsing through uploaded media files an selection of files for items    |
| Messages   | A chat message component to display message history    |
| Multiple   | Lists all child items with the ability to change Data in the list    |
| Number   | A simple number input element    |
| Output   | A simple output of the field value - just for visualization    |
| Password   | A simple passowrd input element    |
| PluginFields   | This component is used for plugin settings and setting up the plugins    |
| SearchSelect   | An advanced selectbox component with the ability to search items within the API    |
| Selectbox   | A simple selectbox imput element    |
| SelectList   | A side by side selection comoponent, where the selected values are printed    |
| Seo   | A basic seo component where common seo relevant settings can be managed    |
| Table   | Display and manage child data in a table view    |
| Text   | A simple text input element    |
| Textarea   | A simple textarea input element for long texts without styling    |


For further config options of the components please check [field configurations](FIELD_CONFIGURATIONS.md).