# xd_aiai_group_DEV4

Dit is het groep project voor development 4: Team 6: leden: Wouter Waumans, Charlotte Jeuris, Tjerk Symens

Er wordt gebruik gemaakt van een database AIAI met
tabel users met daarin id, email, username, password, firstname, lastname, validationcode, validated, image, credits, moderator, canEdit, canDelete, canApprove
tabel prompts met daarin id, prompt, user_id, image, price, details, approved
tabel comments met daarin id, text, prompt_id, user_id
tabel likes met daarin id, prompt_id, user_id, date_created
tabel favourites met daarin id, prompt_id, user_id
tabel user_relations met daarin id, user_id, followed_id
tabel bought_prompts met daarin id, user_id, prompt_id

Als je een werkende reset password wil, moet je de url in de User class en resetPassword functie handmatig aanpassen zodat deze lokaal werkt.
Dit moet gebeuren via de config
