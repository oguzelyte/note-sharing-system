# Student Note Sharing System

Expanded on a note sharing system using Newcastle University's Prof. Lindsay Marshall's framework.

# Brief

* Students register with the system and can upload lecture notes that they would like to share with others or browse the notes that others have uploaded.
* Uploaded notes should identify the module and lecture for which they are relevant.
* This system should include some kind of gamification to reward people whose notes are frequently downloaded by others, such as a leader board or badges - the exact mechanism is up to you to design.
* You may want to consider adding a rating system to indicate how other users perceive the quality of the notes.
* Users who have uploaded notes should be able to upload newer versions and delete notes if they want to.
* There should also be an administration interface for staff who can remove or restrict access to submitted notes where necessary.

# Technology Used

PHP, Twig and Redbean on the server side using Prof. Lindsay Marshall's framework provided, and  HTML5, jQuery and Bootstrap at the client side.

The Framework already provdes user registration, login and file upload features. It also supports a role based access control system  that will allow you to differentiate between (for example) staff and students.

# Style Rules
All your code must rigourously obey the formatting rules that I described in the lectures:

1. Strict separation of content: NO HTML in PHP files.

2. Braces occur on their own directly under the construct they are forming a block for. Indentaton increases after the line with the brace. Braces should always be used - no single line blocks.

3. A single space after reserved words (e.g. if, foreach)

4. Spaces around operators., i.e $j = $k + $b; not $j=$k+$b

5. Indentation is 4 spaces per level. All code starts indented 4 spaces (except for block comments which are left aligned, thus :

/*

 *

 */

6. Every PHP files starts with a comment identifying the content and with suitable PHPDoc commentary. PHPDoc must be used where appropriate throughout the PHP code.

7. HTML should be standard HTML5 but should be styled as XHTML, i.e. lower case tags, /> at the end of singleton tags (e.g. <br/>)

8. You should SASS when writing any extra CSS code.

9. SASS/CSS must be formatted according to these guidelines.

10.  Please try to use JSDoc for any JavaScript you write.

# Deliverables

Moodboard, Fontboard and Storyboard.

DO NOT submit anything using Publisher format - I have no way of reading the files.

Also a file (as txt, docx or pdf) containing a very brief, informal, textual description of your understanding of the requirements and your approach to meeting them. 

# Code

Single compressed archive file (zip, tgz) containing the whole directory in which I developed the website for the brief.
