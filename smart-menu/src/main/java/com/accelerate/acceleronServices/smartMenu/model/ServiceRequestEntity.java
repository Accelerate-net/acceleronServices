package com.accelerate.acceleronServices.smartMenu.model;

import com.fasterxml.jackson.annotation.JsonFormat;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.persistence.*;
import javax.validation.constraints.NotNull;
import java.util.Date;

@Entity
@Data
@AllArgsConstructor
@NoArgsConstructor
@Table(name = "smart_service_requests")
public class ServiceRequestEntity {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotNull
    private String request;

    @NotNull
    private String branch;

    @NotNull
    @Column(name = "table_code")
    private String tableCode;

    @NotNull
    @Column(name = "fk_qr_code")
    private String qrCode;

    @NotNull
    private int status;

    @NotNull
    @Column(name = "fk_steward_code")
    private String stewardCode;

    @JsonFormat(pattern = "yyyy-MM-dd@HH:mm:ss.SSS")
    @Column(name = "date_created")
    private Date createdDate;

    @JsonFormat(pattern = "yyyy-MM-dd@HH:mm:ss.SSS")
    @Column(name = "date_updated")
    private Date updatedDate;

    @NotNull
    @Column(name = "is_active")
    private int isActive;
}
