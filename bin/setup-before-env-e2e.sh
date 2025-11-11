echo "[Before] Setting up environment for e2e tests"

echo "* The working directory is: $(pwd)"

echo "[1] Transform into a plugin"
if [ ! -f "./themegrill-sdk.php" ]; then
  cp ./bin/themegrill-sdk.php ./
else
  echo "[Skip] The plugin file already exists"
fi
