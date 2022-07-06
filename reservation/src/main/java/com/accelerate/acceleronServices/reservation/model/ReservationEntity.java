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
@Table(name = "z_reservations")
public class ReservationEntity {
    @Id
    @NotNull
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    int id;

    @NotNull
    String stamp;

    @NotNull
    @Column(columnDefinition = "integer default 0")
    int status;

    @NotNull
    @Column(name = "userId")
    String mobileNo;

    @NotNull
    @Column(name = "userName")
    String userName;


    @Column(name = "userEmail")
    String userEmail;

    @NotNull
    String outlet;

    @NotNull
    @Column(name = "channel")
    String channel;

    @NotNull
    String date;

    @NotNull
    String time;

    @NotNull
    String count;

    @NotNull
    @Column
    String comments;



    @Column(name ="isBirthday" ,columnDefinition = "integer default 0")
    int isBirthday;

    @NotNull
    @Column(name = "isAnniversary",columnDefinition = "integer default 0")
    int isAnniversary;




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
