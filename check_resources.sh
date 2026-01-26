#!/bin/bash
# Script: check_resources.sh
# Verifica recursos e limitações do ambiente Codespace/Linux

set -e

echo "==== CPU & RAM ===="
free -h

echo "\n==== CPU Load ===="
top -b -n 1 | head -15

echo "\n==== Processos que mais consomem memória ===="
ps aux --sort=-%mem | head -20

echo "\n==== Espaço em disco ===="
df -h

echo "\n==== Limites do sistema ===="
ulimit -a

echo "\n==== Informações do container ===="
cat /etc/os-release
uname -a

if [ -f /proc/cpuinfo ]; then
  echo "\n==== CPUs ===="
  grep 'model name' /proc/cpuinfo | uniq
  grep -c ^processor /proc/cpuinfo | xargs echo "Total CPUs:"
fi

if [ -f /proc/meminfo ]; then
  echo "\n==== Memória Detalhada ===="
  cat /proc/meminfo | head -20
fi

if [ -d /workspaces/ClubManager ]; then
  echo "\n==== Diretórios grandes (>100MB) ===="
  du -h --max-depth=2 /workspaces/ClubManager | grep '[0-9]\{3,\}\.[0-9]\{1,\}M\|G' | sort -hr
fi

echo "\n==== Fim da verificação ===="
