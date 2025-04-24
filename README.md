# Introduction
Welcome to the Xibo Manual.

This repository contains the source content for the Xibo manual in markdown format. 

## Support
Please track all issues in this repository here: https://github.com/OpenSignage/xibo-manual/issues

## Building
The manual is built by running generate.php.

```
php generate.php [template]
```
If template is not specified, the default template will be used.

### Feature Support Tags
The manual supports special tags to display feature compatibility across different platforms. The `{feat}` tag is used to create feature support cards that show compatibility information.

#### Usage
```
{feat}feature_name|version{/feat}
```

- `feature_name`: The identifier of the feature as defined in the language-specific `features.json`
- `version`: The version number of the CMS (e.g., "v4")

#### Example
```
{feat}dashboard|v4{/feat}
```

This will generate a feature card showing:
- Feature name
- CMS availability version
- Support status for each platform:
  - Android
  - Tizen
  - Ubuntu
  - Windows
  - WebOS
  - ChromeOS

The support status is pulled from the language-specific `features.json` file located in `source/<lang>/tag/features.json`.

### Docker
It is also possible to build the manual using Docker, resulting in a Docker
image which hosts the complete manual and a web server.

To do this issue the command:

```
./build.sh -t default -r xibo-manual
```

Where `-t` is your theme name and `-r` is the name with which to tag the 
container.

Themes must exist in `/template/custom/<theme_name>` to be built. They 
are built using inheritance from the default theme.

## Translations
The `translator.php` script provides automated translation functionality from English to other languages (primarily Japanese) using the Google Cloud Translation API.

### Prerequisites
1. Google Cloud Translation API Key
   - Create a file named `apikey.txt` in the root directory
   - Add your Google Cloud Translation API key to this file

2. Configuration File (Optional)
   - Create `translateConfig.json` to customize translation settings
   - Default configuration:
     ```json
     {
       "sourceLanguage": "en",
       "targetLanguage": "ja",
       "inputDir": "source/en/",
       "outputDir": "source/ja/"
     }
     ```

3. Glossary (Optional)
   - Create `glossary.json` to define custom translations for specific terms
   - Format:
     ```json
     {
       "source term": "target translation"
     }
     ```

4. Exclusion List (Optional)
   - Create `exclude.json` to specify terms that should not be translated
   - Terms in this list will be preserved in their original form

### Usage
```bash
php translator.php [file1.md file2.md ...]
```
- If no files are specified, all .md files in the source directory will be translated
- Translated files are saved in the output directory with the same filename
- The script preserves special formatting and protected terms

### Features
- Automated translation using Google Cloud Translation API
- Custom glossary support for consistent terminology
- Exclusion list for protecting specific terms from translation
- Batch processing of multiple files
- Configurable source and target directories
- Preservation of markdown formatting

### Directory Structure
```
xibo-manual/
├── translator.php
├── translateConfig.json
├── glossary.json
├── exclude.json
├── apikey.txt
├── source/
│   ├── en/
│   │   └── *.md
│   └── ja/
│       └── *.md
```

Japanese translation is handled by Open Source Digital Signage Initiative.

## AI Assistant (Custom function)
The AI Assistant feature provides an interactive help system powered by Google's Gemini AI. This feature allows users to ask questions about Xibo in natural language and receive contextually relevant answers.

### Features
- Real-time AI-powered responses
- Context-aware answers based on manual content
- Support for natural language queries
- Interactive chat-like interface
- Automatic source reference linking

### Setup Requirements
1. Google Gemini API Key
   - Create a file named `gemini_api_key.txt` in the `/ai` directory
   - Add your Gemini API key to this file

2. Manual Data
   - Ensure `learning_data.json` exists in the `/ai` directory
   - This file contains the preprocessed manual content

3. System Prompt
   - Create `system_prompt.txt` in the `/ai` directory
   - This file contains the AI assistant's personality and response guidelines

### Configuration
The AI Assistant can be configured through the following files:
- `aiSearchEngine.php`: Main backend processing
- `assistant.html`: Frontend interface
- `assistantStyle.css`: UI styling

### Usage
1. Access the AI Assistant through the manual interface
2. Type your question in natural language
3. Receive AI-generated responses with relevant manual references
4. Click provided links to access detailed documentation

### Technical Details
- Uses Server-Sent Events (SSE) for real-time streaming responses
- Implements semantic search for context-relevant information
- Provides source attribution with direct links to manual sections
- Supports both Japanese and English responses

## Google Search Engine (Custom function)
You can search within the manual pages using the Google programmable search engine.
To use this function, you need to register your search site.
[Google Programmable Search Engine](https://programmablesearchengine.google.com)

### Google Search Console
It takes a significant amount of time for Google to crawl the manual pages you host on your site.
To make your site searchable as quickly as possible, we recommend that you register the URL of your manual page in the [Google Search Console](https://search.google.com/search-console).

### Video Support
The manual now supports embedding YouTube videos using the `{video}` tag. This feature allows you to add interactive video content to your documentation.

#### Usage
```
{video}VIDEO_ID{/video}
```
- `VIDEO_ID`: The YouTube video ID (e.g., "dQw4w9WgXcQ" from the URL https://www.youtube.com/watch?v=dQw4w9WgXcQ)

#### Features
- Responsive video thumbnails
- Modal popup player
- Automatic thumbnail loading from YouTube
- Mobile-friendly interface
- Lazy loading of video content

#### Example
```
{video}dQw4w9WgXcQ{/video}
```

This will generate:
- A clickable video thumbnail
- A modal dialog with embedded YouTube player
- Responsive layout that works on all devices

## TODO
1. Support translation dictionary.
