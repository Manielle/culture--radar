FROM ubuntu:22.04

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    curl \
    git \
    python3 \
    python3-pip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Créer un répertoire de travail
WORKDIR /workspace

# Exposer un port si nécessaire
EXPOSE 8080

# Commande par défaut
CMD ["/bin/bash"]