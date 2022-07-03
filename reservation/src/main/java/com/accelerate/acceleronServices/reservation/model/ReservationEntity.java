package com.accelerate.acceleronServices.reservation.model;

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
@Table(name = "reservation")
public class ReservationEntity {
    @Id
    @NotNull
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    String name;

    @Column(name = "mobile_no")
    @NotNull
    String mobileNo;


    /*

    @JsonFormat(pattern = "yyyy-MM-dd@HH:mm:ss.SSS")
    @Column(name = "date_created")
    private Date createdDate;

    @JsonFormat(pattern = "yyyy-MM-dd@HH:mm:ss.SSS")
    @Column(name = "date_updated")
    private Date updatedDate;

    @NotNull
    @Column(name = "is_active")
    private int isActive;

     */
}
