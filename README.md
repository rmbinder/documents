# documents

'documents' is an Admidio plugin. It allows you to display user-related documents in a member's profile.

Several requirements must be met for this to happen:
  
1. All documents must be located in a 'Documents & Files' folder
  
2. All documents must be preceded by a sequential number
     e.g. '0015-Mustermann Max-declaration of membership.pdf'
     or '65-Meier Franz-termination.jpg'
 
3. The sequential number can either be 
    - the member's 'usr_id'
    - the member's 'usr_uuid'
    - or a profile field with a sequential number (e.g. member number)
         (Note: The Membership Fee plugin can create corresponding membership numbers)
         
The plugin checks whether the sequential number of a document matches the member number (usr_id, usr_uuid or member numer) and then displays the document.
