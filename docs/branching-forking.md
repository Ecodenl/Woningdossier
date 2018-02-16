# Branching and forking

As most projects do, we keep a develop and a master branch. We also work in 
feature branches.
We follow the principle of [GitFlow](https://datasift.github.io/gitflow/GitFlowForGitHub.html) for developing.

## master
The master branch contains checked and accepted functionality. This version 
can be deployed at any time

## develop
This is the branch containing all functionality that is done and shared among 
the development team. This branch may contain features that will be available 
in next release.

## feature/*
The feature branches are created per requested functionality. All that has to do 
with the functionality (code, migrations, documentation, tests) should be in here
so the full feature branch is tested before it can be merged via a pull request.

## tagging
Once the master branch is in a state that it can be released (this is determined 
by the Econobis group) the master branch will be tagged with a version number 
and this version will be deployed on a production environment.