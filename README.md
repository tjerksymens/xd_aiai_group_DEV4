# xd_aiai_group_DEV4

Dit is het groep project voor development 4: Team 6: leden: Wouter Waumans, Charlotte Jeuris, Tjerk Symens

Er wordt gebruik gemaakt van een database AIAI met
tabel users met daarin id, email, username, password, firstname, lastname, validationcode, validated, image
tabel prompts met daarin id, prompt, user_id, image, price, details
tabel comments met daarin id, text, prompt_id, user_id
tabel likes met daarin id, prompt_id, user_id, date_created

Als je een werkende reset password wil, moet je de url in de User class en resetPassword functie handmatig aanpassen zodat deze lokaal werkt.
Dit moet gebeuren via de config
