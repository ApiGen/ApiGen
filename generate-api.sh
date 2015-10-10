# Generate Api
bin/apigen generate -s src -d ../gh-pages
cd ../gh-pages

# Set identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Add branch
git init
git remote add origin https://${GH_TOKEN}@github.com/ApiGen/api.git > /dev/null
git checkout -B gh-pages

# Push generated files
git add .
git commit -m "Api updated"
git push origin gh-pages -fq > /dev/null
