#!/bin/sh

#compact

sass --style compressed  --watch scss/themes/:../css/ scss/main.scss:../css/main.css
