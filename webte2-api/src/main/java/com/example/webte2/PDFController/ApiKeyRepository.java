package com.example.webte2.PDFController;

import org.springframework.data.jpa.repository.JpaRepository;

import java.beans.JavaBean;

public interface ApiKeyRepository extends JpaRepository<ApiKey, Long> {
    ApiKey findByApiKey(String api_key);
}
