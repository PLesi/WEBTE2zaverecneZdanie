package com.example.webte2.PDFController;

import jakarta.persistence.*;

@Entity
@Table(name = "api_keys", indexes = {
        @Index(name = "idx_user_id", columnList = "user_id"),
        @Index(name = "idx_api_key", columnList = "api_key")
})
public class ApiKey {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "api_key", length = 64, nullable = false, unique = true)
    private String apiKey;

    @Column(name = "user_id", nullable = false)
    private Long userId;

    // Getters and setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getApiKey() {
        return apiKey;
    }

    public void setApiKey(String apiKey) {
        this.apiKey = apiKey;
    }

    public Long getUserId() {
        return userId;
    }

    public void setUserId(Long userId) {
        this.userId = userId;
    }
}