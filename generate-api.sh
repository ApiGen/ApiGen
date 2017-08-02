#!/usr/bin/env bash

# Generate Api
bin/apigen generate src --destination gh-pages
cd ../gh-pages

# Git identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Add branch
git init
git remote add origin https://${GH_TOKEN}@github.com/ApiGen/api.git > /dev/null
git checkout -B gh-pages

# Commit & push
git add .
git commit -m "API Regenerated"
git push origin gh-pages -fq > /dev/null
