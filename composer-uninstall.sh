export composer_uninstall='() {
  composer remove --no-update "$@"
  composer update --dry-run |
  grep -Eo -e "- Uninstalling\s+\S+" |
  cut -d" " -f3 |
  xargs composer update
}'

exec $SHELL -l
