<div class=" col-md-6">
    <div style="text-align: justify">
        <p>
            <b>About Sabasaba Ticket</b>, This are
            Digital entry tickets produced
            through digital systems, this
            includes Adult Entry ticket,Child
            Entry Ticket, Daily Outside Car
            Parking and VIP Tickets. This tickets are sent to customer through phone number, in one to two
            minutes after receiving customer payments. Also customer has the ability to request his or her ticket through special feature at 
            <a href="{{ route('myTicket.edit',['myTicket'=>date('sidymihsdyomn').date('sydsysdsj')]) }}">My sabasaba Ticket</a>
        </p>
        <p>
            <b>List of tickets provided online:-</b><br>
            <b>Children Entry Ticket (CET)</b>,
            This are special Ticket for
            children who
            are in schools at secondary level
            and bellow, this ticket cost 1,000
            TZS for one ticket and it's
            for <b class=" text-danger">ONE
                ENTRY ONLY!</b>
            <br>
            <b>Adult Entry Ticket (AET)</b>,
            This are special Ticket for
            Adults,this
            ticket cost <b>3,000 TZS for normal
                exhibition day</b> and <b>4,000
                TZS for SabaSaba Day
                (07-07-2024)</b> for one ticket,
            this ticket are used for <b class=" text-danger">ONE ENTRY
                ONLY!</b>
            <br>

            <b>Day
                Car Parking Outside (CPT)</b>,
            This are special Ticket for car
            parking,this
            ticket cost <b>10,000 TZS for One
                Ticket</b> this ticket are used
            for <b class=" text-danger">ONE
                PARKING, PER DAY ONLY!</b>
            <br>
            <b>SabaSaba VIP Tickets (VET)</b>,
            This are special Ticket for VIP
            personel,this
            ticket cost <b>1,500,000 TZS for One
                Ticket.</b> this ticket includes
            the following benefits:-
            @foreach ($benefits as $item)
                <b>{{ $item->benefit_name }}</b>,
            @endforeach .

        </p>

        <p>
            <b>Why online Ticket ?</b>
            <br>
            <b>Time-Saving</b>, Quick and
            Efficient: Purchasing tickets online
            is a fast
            process, saving customers time that
            would be spent waiting in
            line at a physical
            ticket counter at Sabasaba area.
            Instant Confirmation: Customers
            receive immediate confirmation
            of their ticket.
            <br>

            <b>Convenience & Accessibility</b> :
            Customers can purchase
            tickets from the
            comfort of their homes
            using a computer or mobile device.
            <br><b>24/7 Availability</b>: Online
            ticketing platforms are
            available around the
            clock,
            allowing customers to buy tickets at
            any time


            <b>Real-Time Updates:</b>
            All visitors with online Tickets,
            will be able to receive
            real-time updates and
            notifications about the event, such
            as changes in schedule or venue,
            enhancing their overall
            experience and time
            table.
        </p>

    </div>





</div>
