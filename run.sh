# https://stackoverflow.com/questions/19950620/how-to-hide-password-in-command-line-with-and-get-the-value-into-bat-file

echo "Welcome to the FileCommitAnimator! You'll need a Github account to continue."
echo ""
prompt="Enter Github Username: "
while IFS= read -p "$prompt" -r -s -n 1 char 
do
if [[ $char == $'\0' ]];     then
    break
fi
if [[ $char == $'\177' ]];  then
    prompt=$'\b \b'
    password="${username%?}"
else
    prompt="$char"
    username+="$char"
fi
done
echo " "

prompt="Enter Password: "
while IFS= read -p "$prompt" -r -s -n 1 char 
do
if [[ $char == $'\0' ]];     then
    break
fi
if [[ $char == $'\177' ]];  then
    prompt=$'\b \b'
    password="${password%?}"
else
    prompt='*'
    password+="$char"
fi
done
echo " "
echo " "
php create.php "$username" "$password"
